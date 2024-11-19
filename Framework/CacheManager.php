<?php
// Path: src/vendor/framework/framework/Framework/CacheManager.php
namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class CacheManager
{
    private static $instance = null;
    private $cacheAdapter;
    private $sub;

    private function __construct()
    {
        $this->sub =  $_ENV['REDIS_CACHE_PREFIX'] ?? 'cache_';
        $this->initializeCacheAdapter();                
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeCacheAdapter()
    {
        $redisConnection = RedisConnection::instance();
        $redis = $redisConnection->getRedis();

        if ($redis) {
            try {
                $this->cacheAdapter = new RedisAdapter($redis, $this->sub, 0);
            } catch (\Exception $e) {
                // Handle the exception and fallback to FilesystemAdapter                
                $this->cacheAdapter = new FilesystemAdapter();
            }
        } else {
            // Use the default file system cache adapter
            
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
            self::$instance->cacheAdapter->clear();
        }
    }

    public function logDebugBar($key, $data, $expiration = 0)
    {
        if (class_exists('Framework\DebugBar') && DebugBar::isSet()) {
            $debugbar = DebugBar::instance()->getDebugBar();            
            if ($debugbar && isset($debugbar[$this->sub])) {
                $debugbar[$this->sub]->addCacheItem($key, json_encode($data));
            }
        }
    }
}
