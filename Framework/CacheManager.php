<?php

namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheManager
{
    private static $instance = null;
    private $cacheAdapter;

    private function __construct()
    {
        $this->initializeCacheAdapter();
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new CacheManager();
        }
        return self::$instance;
    }

    private function initializeCacheAdapter()
    {
        try {
            $redis = RedisConnection::instance()->getRedis();
            $this->cacheAdapter = new RedisAdapter($redis, 'cache_', 0);
        } catch (\Exception $e) {
            // Utiliser le gestionnaire de cache par défaut basé sur le système de fichiers
            $this->cacheAdapter = new FilesystemAdapter();
        }
    }

    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }

    public static function clear()
    {
        if (self::$instance !== null) {
            self::instance()->cacheAdapter->clear();
        }
        return;
    }

    public function logDebugBar($key, $data, $expiration)
    {
        if ($expiration > 0) {
            if (class_exists('Framework\DebugBar') && DebugBar::isSet()) {
                $debugbar = DebugBar::Instance()->getDebugBar();
                $debugbar["cache"]->addCacheItem($key, json_encode($data));
            }
        }
    }
}
