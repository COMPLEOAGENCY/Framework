<?php

namespace Framework;

use DebugBar\StandardDebugBar;
use Framework\DataCollectors\CacheCollector;
use Framework\DataCollectors\ServerVariablesCollector;

class DebugBar
{
    private static $instance = null;
    private $debugBar;

    private function __construct()
    {
        $this->debugBar = new StandardDebugBar();
        
        $cacheCollector = new CacheCollector();
        $this->debugBar->addCollector($cacheCollector);

        $serverVariablesCollector = new ServerVariablesCollector();
        $this->debugBar->addCollector($serverVariablesCollector);
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
        return self::$instance !== null;
    }

    public function getDebugBar()
    {
        return $this->debugBar;
    }
}



