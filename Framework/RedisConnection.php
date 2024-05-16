<?php

namespace Framework;

class RedisConnection
{
    private static $instance = null;
    private $redis;

    private function __construct()
    {
        $this->connect();
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new RedisConnection();
        }
        return self::$instance;
    }

    private function connect()
    {
        $redisHost = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
        $redisPort = $_ENV['REDIS_PORT'] ?? 6379;

        $this->redis = new \Redis();
        $this->redis->connect($redisHost, $redisPort);

        // Vérifier si la connexion à Redis fonctionne
        if (!$this->redis->ping()) {
            throw new \RuntimeException('Ping to Redis failed.');
        }
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
