<?php

namespace Framework;
use Classes\Logger;

class RedisConnection
{
    private static $instance = null;
    private $redis;
    private $lastError = null;
    private $connectionStatus = null;

    private function __construct()
    {
        if (class_exists('Redis')) {
            $this->connect();
        } else {
            $this->lastError = 'Redis extension is not installed';
            $this->connectionStatus = 'Extension PHP Redis manquante';
            Logger::critical('Redis', ['error' => $this->lastError]);
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
        $redisPassword = $_ENV['REDIS_PASSWORD'] ?? null;

        try {
            $this->redis = new \Redis();
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);

            // Tentative de connexion avec timeout
            if (!$this->redis->connect($redisHost, $redisPort)) {
                throw new \RuntimeException("Connection failed to Redis server at {$redisHost}:{$redisPort}");
            }
            $this->connectionStatus = "Connected to Redis at {$redisHost}:{$redisPort}";

            // Authentification si un mot de passe est configuré
            if ($redisPassword !== null) {
                if (!$this->redis->auth($redisPassword)) {
                    throw new \RuntimeException('Redis authentication failed - Invalid password');
                }
                $this->connectionStatus .= " (Authenticated)";
            }

            // Vérifier si la connexion à Redis fonctionne
            $pingResponse = $this->redis->ping();
            if (!$pingResponse) {
                throw new \RuntimeException("Redis ping failed - No response");
            }

            // Récupérer des informations sur le serveur Redis
            $info = $this->redis->info();
            $this->connectionStatus .= sprintf(
                " | Redis v%s | Memory: %s | Connected clients: %d",
                $info['redis_version'],
                $info['used_memory_human'],
                $info['connected_clients']
            );

            // Logger::debug('Redis Connection Success', [
            //     'status' => $this->connectionStatus,
            //     'info' => $info
            // ]);

        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            $this->connectionStatus = "Connection failed: " . $this->lastError;
            Logger::critical('Redis Connection Error', [
                'error' => $this->lastError,
                'host' => $redisHost,
                'port' => $redisPort
            ]);
            $this->redis = null;
        }
    }

    public function getRedis()
    {
        return $this->redis;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function getConnectionStatus()
    {
        return $this->connectionStatus;
    }

    public function checkRedisStatus()
    {
        if ($this->redis === null) {
            return [
                'connected' => false,
                'status' => $this->connectionStatus,
                'error' => $this->lastError
            ];
        }

        try {
            $pingResponse = $this->redis->ping();
            if (!$pingResponse) {
                throw new \RuntimeException("Redis ping failed");
            }
            
            $info = $this->redis->info();
            return [
                'connected' => true,
                'status' => $this->connectionStatus,
                'info' => [
                    'version' => $info['redis_version'],
                    'memory_usage' => $info['used_memory_human'],
                    'connected_clients' => $info['connected_clients'],
                    'uptime_days' => $info['uptime_in_days'],
                    'total_commands_processed' => $info['total_commands_processed']
                ]
            ];
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            Logger::critical('Redis Status Check Failed', ['error' => $this->lastError]);
            
            return [
                'connected' => false,
                'status' => 'Connection check failed',
                'error' => $this->lastError
            ];
        }
    }
}