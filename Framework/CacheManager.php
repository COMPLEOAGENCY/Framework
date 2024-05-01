<?php
namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class CacheManager
{
    private static $instance = null;
    private $cacheAdapter;

    private function __construct()
    {
        // Initialise ici avec l'adaptateur de cache souhaité
        $this->cacheAdapter = new FilesystemAdapter();
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new CacheManager();
        }
        return self::$instance;
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
                // $debugbar["request"]->addArray($key, array($data));
                // $debugbar["messages"]->addMessage($key, json_decode(json_encode($data),true));
            }
        }
    }
}
