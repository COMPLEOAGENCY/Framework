<?php
namespace  Framework;

use DebugBar\StandardDebugBar;
use Framework\DataCollectors\CacheCollector;


class DebugBar
{
    private static $instance = null;
    private $debugBar;

    private function __construct()
    {
        $this->debugBar = new StandardDebugBar();
        $cacheCollector = new CacheCollector();
        $this->debugBar->addCollector($cacheCollector);        
    }

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new DebugBar();
        }
        return self::$instance;
    }

    public static function isSet()
    {
        if (self::$instance === null) {
            return false;
        }
        return true;
    }    

    public function getDebugBar()
    {
        return $this->debugBar;
    }
}


