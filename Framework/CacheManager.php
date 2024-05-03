<?php
namespace Framework;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheManager
{
    private static $instance = null;
    private $cacheAdapter;

    private function __construct()
    {
        // Initialisez ici avec l'adaptateur de cache souhaité
        $this->cacheAdapter = new FilesystemAdapter();
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new CacheManager();
        }
        return self::$instance;
    }

    public static function isSet()
    {
        return self::$instance !== null;
    }

    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }
}



