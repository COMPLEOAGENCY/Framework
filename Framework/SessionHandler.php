<?php
// Path: src/vendor/framework/framework/Framework/SessionHandler.php

namespace Framework;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

class SessionHandler
{
    private static $instance = null;  // Singleton instance
    private static $session = null;   // Propriété statique pour la session Symfony
    private $storageHandler = null;   // Handler pour le backend de session
    private $sub;

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct()
    {
        // Ne rien faire ici, tout sera fait dans startSession()
        $this->sub = $_ENV['REDIS_SESSION_PREFIX'] ?? 'session_:';
    }

    /**
     * Retourne l'instance unique du SessionHandler (Singleton)
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // Démarre la session lors de la récupération de l'instance
        self::$instance->startSession();

        return self::$instance;
    }

    /**
     * Démarre la session si elle n'est pas encore démarrée
     */
    public function startSession(): void
    {
        try {
            if (self::$session === null || !(self::$session instanceof Session)) {
                // Initialiser le stockage si ce n'est pas déjà fait
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_write_close();
                }            
                $this->initializeStorage();
                self::$session = new Session($this->storageHandler);
            }
    
            // Démarrer la session si elle n'est pas déjà démarrée
            if (!self::$session->isStarted()) {
                self::$session->start();  // C'est ici que l'erreur Symfony se produit
            }
        } catch (\Throwable $e) {
            // Log l'erreur pour le debugging
            error_log("Session start error: " . $e->getMessage());
            
            // Essayer de créer une session de secours en mémoire
            $this->fallbackToMemorySession();
            
            // Si en mode développement, propager l'erreur
            if ($_ENV['APP_ENV'] === 'dev') {
                throw new \Exception("Session initialization failed: " . $e->getMessage());
            }
        }
    }
    
    private function fallbackToMemorySession(): void
    {
        try {
            // Créer un stockage en mémoire temporaire
            $this->storageHandler = new NativeSessionStorage([
                'session.storage.native_options' => [
                    'use_cookies' => false,
                    'use_only_cookies' => false,
                    'use_trans_sid' => false,
                    'cookie_httponly' => true,
                ]
            ]);
            self::$session = new Session($this->storageHandler);
            
            // Démarrer la session en mémoire
            self::$session->start();
            
            // Logguer l'utilisation du fallback
            error_log("Using memory session fallback due to storage initialization failure");
        } catch (\Throwable $e) {
            // En cas d'échec du fallback, logger mais continuer
            error_log("Memory session fallback failed: " . $e->getMessage());
        }
    }

    /**
     * Initialise le stockage de la session avec Redis ou bascule sur les fichiers
     */
    private function initializeStorage(): void
    {
        try {
            $sessionDriver = $_ENV['SESSION_DRIVER'] ?? 'native';
    
            // Si une session est déjà active, on la ferme proprement
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }
    
            if ($sessionDriver === 'redis') {
                try {
                    $redisConnection = RedisConnection::instance();
                    $redis = $redisConnection->getRedis();
    
                    if ($redis) {
                        $redisHandler = new RedisSessionHandler($redis, ['prefix' => $this->sub]);
                        $this->storageHandler = new NativeSessionStorage([], $redisHandler);
                        return;
                    }
                } catch (\Throwable $e) {
                    trigger_error("Session Redis demandée mais impossible de se connecter. Bascule sur le système de fichiers.", E_USER_WARNING);
                }
            }
    
            // Utiliser systématiquement SESSION_PATH s'il est défini, sinon sys_get_temp_dir()
            $sessionPath = $_ENV['SESSION_PATH'] ?? sys_get_temp_dir();
    
            // Vérifier que le dossier existe
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0755, true);
            }
    
            $options = [
                'session.storage.native_options' => [
                    'use_strict_mode' => true,
                    'use_cookies' => true,
                    'use_only_cookies' => true,
                    'cookie_httponly' => true,
                    'auto_start' => false,
                    'save_path' => $sessionPath
                ]
            ];
    
            $fileHandler = new NativeFileSessionHandler($sessionPath);
            $this->storageHandler = new NativeSessionStorage($options, $fileHandler);
    
        } catch (\Throwable $e) {
            throw new \Exception("Cannot Initialize Session Storage: " . $e->getMessage());
        }
    }

    /**
     * Méthode magique pour rediriger les appels vers l'objet Session de Symfony
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        // Vérifier que la session est démarrée avant d'effectuer des appels sur l'objet Session
        if (!self::$session->isStarted()) {
            $this->startSession();
        }
        // Si la méthode existe dans l'objet Session, l'appeler
        if (method_exists(self::$session, $method)) {
            return call_user_func_array([self::$session, $method], $args);
        }

        // Si la méthode n'existe pas, lancer une exception
        throw new \BadMethodCallException("La méthode {$method} n'existe pas dans " . get_class($this));
    }

    /**
     * Efface et détruit la session en cours
     */
    public function clearSession(): void
    {
        if (self::$session !== null && self::$session->isStarted()) {
            self::$session->clear();      // Vider la session
            self::$session->invalidate(); // Invalider la session (détruire l'ID de session)
            //session_write_close();        // Fermer la session pour éviter de nouvelles écritures
        }
    }

    /**
     * Sauvegarde explicitement la session et ne ferme pas la session
     */
    public function persistNow(): void
    {
        if (self::$session !== null && self::$session->isStarted()) {
            self::$session->save();  // Sauvegarder la session et persiste les données vers redis ou le fichier ne ferme pas la session comme en PHP natif  
        }
    }
}