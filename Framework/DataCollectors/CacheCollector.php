<?php

namespace Framework\DataCollectors;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Framework\CacheManager;

class CacheCollector extends DataCollector implements Renderable
{
    protected $data = [];

    public function addCacheItem($key, $value)
    {
        // Vous pouvez choisir de stocker simplement la valeur ou des détails supplémentaires
        $this->data[$key] = [
            'value' => $value,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public function collect()
    {
        return [
            'size' => count($this->data),
            'data' => $this->data
        ];
    }

    public function getName()
    {
        return 'cache';
    }

    public function getWidgets()
    {
        return [
            "cache" => [
                "icon" => "database",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "cache.data",
                "default" => "{}",
                "title" => "Cache"
            ],
            "cache:size" => [
                "icon" => "dashboard",
                "tooltip" => "Number of cache entries",
                "map" => "cache.size",
                "default" => "0",
                "badge" => "cache.size" // S'assurer que 'cache.size' renvoie un nombre
            ]
        ];
    }
    
}

