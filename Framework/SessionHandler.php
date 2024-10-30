<?php

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

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct()
    {
        // Ne rien faire ici, tout sera fait dans startSession()
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
        if (self::$session === null || !(self::$session instanceof Session)) {
            // Initialiser le stockage si ce n'est pas déjà fait
            $this->initializeStorage();
            self::$session = new Session($this->storageHandler);
        }

        // Démarrer la session si elle n'est pas déjà démarrée
        if (!self::$session->isStarted()) {
            self::$session->start();
        }
    }

    /**
     * Initialise le stockage de la session avec Redis ou bascule sur les fichiers
     */
    private function initializeStorage(): void
    {
        try {
            // Vérifier d'abord le driver de session configuré
            $sessionDriver = $_ENV['SESSION_DRIVER'] ?? 'native';

            if ($sessionDriver === 'redis') {
                // Tentative d'utilisation de Redis pour les sessions
                $redisConnection = RedisConnection::instance();
                $redis = $redisConnection->getRedis();

                if ($redis) {
                    $redisHandler = new RedisSessionHandler($redis, ['prefix' => 'session_:']);
                    $this->storageHandler = new NativeSessionStorage([], $redisHandler);
                    return;
                }
                
                // Si on arrive ici avec redis demandé mais connexion échouée, on log l'erreur
                trigger_error("Session Redis demandée mais impossible de se connecter. Bascule sur le système de fichiers.");
            }

            $sessionStarted = (session_status() === PHP_SESSION_ACTIVE);
        
            $options = [
                'session.storage.native_options' => [
                    'use_strict_mode' => true,
                    'use_cookies' => true,
                    'use_only_cookies' => true,
                    'cookie_httponly' => true,
                    // On configure auto_start selon l'état de la session
                    'auto_start' => !$sessionStarted
                ]
            ];

            // Utilisation du gestionnaire de fichiers par défaut ou en fallback
            $fileHandler = new NativeFileSessionHandler(sys_get_temp_dir());
            $this->storageHandler = new NativeSessionStorage($options, $fileHandler);

        } catch (\Exception $e) {
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