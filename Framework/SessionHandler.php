<?php

namespace Framework;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class SessionHandler
{
    private static $instance = null;
    private static $session = null;

    private function __construct()
    {
        // Ne pas initialiser la session ici pour éviter des opérations inutiles
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function startSession()
    {
        if (self::$session === null) {
            $this->initializeSession();
        }
        return $this;
    }

    private function initializeSession(): void
    {
        try {
            $redisConnection = RedisConnection::instance();
            $redis = $redisConnection->getRedis();
            if ($redis) {
                $redisHandler = new RedisSessionHandler($redis, ['prefix' => 'session_:']);
                $storage = new NativeSessionStorage([], $redisHandler);
                self::$session = new Session($storage);
                self::$session->start();

                if (!self::$session->has('is_redis_used')) {
                    self::$session->set('is_redis_used', true);
                }
            } else {
                throw new \Exception('Redis connection not available.');
            }
        } catch (\Exception $e) {
            self::$session = new Session();
            self::$session->start();
        }
    }

    public function getSession(): ?Session
    {
        return self::$session;
    }

    public function clearSession(): void
    {
        if (self::$session !== null) {
            self::$session->clear();
            self::$session = null;
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        }
    }

    public function __call($method, $args)
    {
        if (self::$session !== null && method_exists(self::$session, $method)) {
            return self::$session->{$method}(...$args);
        }
        return false;
    }
}