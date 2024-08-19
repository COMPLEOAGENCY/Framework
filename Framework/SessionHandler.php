<?php

namespace Framework;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionHandler
{
    private static $instance = null;
    private static $session = null;

    // Verrou pour éviter les conflits d'initialisation de l'instance et de la session
    private static $instanceLock = false;
    private static $sessionLock = false;
    
    // Variable interne pour suivre si Redis est utilisé ou non
    private static $isRedisUsed = true;

    private function __construct()
    {
        // Constructor privé pour empêcher l'instanciation directe
    }

    /**
     * Retourne l'instance unique du SessionHandler
     *
     * @return self
     */
    public static function getInstance(): self
    {

    //TODO  Si la session n'est pas encore démarrée, démarrer la session
            
        // Si l'instance existe déjà, on la retourne immédiatement
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Synchronisation via un verrou pour éviter les problèmes d'accès concurrentiel
        while (self::$instanceLock) {
            usleep(5000); // Attendre 5 millisecondes avant de réessayer
        }

        // Bloquer l'instance pour empêcher l'initialisation simultanée
        self::$instanceLock = true;

        // Si l'instance est encore null, on la crée
        if (self::$instance === null) {
            self::$instance = new self();
        }

        // Libérer le verrou une fois l'instance créée
        self::$instanceLock = false;

        return self::$instance;
    }

    /**
     * Démarre la session si elle n'est pas encore démarrée
     *
     * @return self
     */
    public function startSession()
    {
        // Si la session Symfony est déjà démarrée, on n'a rien à faire
        if (self::$session !== null) {
        // if (self::$session !== null && self::$session->isStarted()) {
            // trigger_error("Session déjà démarrée par Symfony -> renvoi de l'instance");
            return $this;
        }
    
        // Synchronisation via un verrou pour éviter les conflits d'initialisation de la session
        while (self::$sessionLock) {
            usleep(5000); // Attendre 5 millisecondes avant de réessayer
        }
    
        // Bloquer la session pour empêcher l'initialisation simultanée
        self::$sessionLock = true;
    
        try {
            // Initialiser la session uniquement si elle n'est pas encore démarrée
            if (self::$session === null) {
                // trigger_error("Initialisation de la session...");
                $this->initializeSession();
            }
        } catch (\Exception $e) {
            trigger_error("Erreur lors de l'initialisation de la session : " . $e->getMessage());
        } finally {
            // Libérer le verrou de session
            self::$sessionLock = false;
        }
    
        // trigger_error("Session initialisée et démarée avec succès");
    
        return $this;
    }
    

    /**
     * Initialise la session en utilisant Redis ou bascule sur les fichiers en cas d'échec
     */
    private function initializeSession(): void
    {
        try {
            // Tentative d'initialisation avec Redis
            $redisConnection = RedisConnection::instance();
            $redis = $redisConnection->getRedis();

            if ($redis) {
                $redisHandler = new RedisSessionHandler($redis, ['prefix' => 'session_:']);
                $storage = new NativeSessionStorage([], $redisHandler);
                self::$session = new Session($storage);
                self::$session->start();
                self::$isRedisUsed = true;
                // trigger_error("Session démarrée avec Redis");
            } else {
                // throw new \Exception('Redis connection not available.');
            }
        } catch (\Exception $e) {
            trigger_error("Échec de la connexion Redis, bascule vers le système de fichiers : " . $e->getMessage());
            // Fallback vers le système de fichiers en cas d'erreur avec Redis
            $this->initializeFileSession();
        }
    }

    /**
     * Initialise la session en utilisant le système de fichiers natif de PHP
     */
    private function initializeFileSession(): void
    {
        // trigger_error("Tentative de démarrage de session avec le système de fichiers");
        
        // Utilisation d'un handler de session basé sur le système de fichiers natif de PHP
        $fileSessionHandler = new NativeFileSessionHandler(sys_get_temp_dir());  // Utilisation du dossier temporaire
        $storage = new NativeSessionStorage([], $fileSessionHandler);
        
        try {
            self::$session = new Session($storage);
            self::$session->start();
            // trigger_error("Session démarrée avec succès avec le système de fichiers");
        } catch (\Exception $e) {
            // trigger_error("Échec du démarrage de la session avec le système de fichiers : " . $e->getMessage());
            throw $e;
        }
    
        // Mettre à jour la variable locale
        self::$isRedisUsed = false;
    }
    

    /**
     * Retourne si Redis est utilisé pour la session
     *
     * @return bool
     */
    public function isRedisUsed(): bool
    {
        return self::$isRedisUsed;
    }

    /**
     * Efface la session en cours et la détruit
     */
    public function clearSession(): void
    {
        if (self::$session !== null && self::$session->isStarted()) {
            self::$session->clear();
            self::$session = null;
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
                trigger_error("Session détruite");
            }
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
        $this->startSession();

        if (self::$session !== null && method_exists(self::$session, $method)) {
            return self::$session->{$method}(...$args);
        }

        throw new \BadMethodCallException("Méthode {$method} introuvable dans Session");
    }
}
