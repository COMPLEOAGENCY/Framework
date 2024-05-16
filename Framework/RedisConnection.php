<?php

namespace Framework;

class RedisConnection
{
    private static $instance = null;
    private $redis;

    private function __construct()
    {
        if (class_exists('Redis')) {
            $this->connect();
        } else {
            // Log or handle the situation where Redis is not available
            // For example, you can use a logger to record that Redis is not available
            // Logger::warning('Redis extension is not installed.');
        }
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

        try {
            $this->redis = new \Redis();
            $this->redis->connect($redisHost, $redisPort);

            // Vérifier si la connexion à Redis fonctionne
            if (!$this->redis->ping()) {
                throw new \RuntimeException('Ping to Redis failed.');
            }
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            // Logger::error('Unable to connect to Redis: ' . $e->getMessage());
            $this->redis = null;
        }
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
