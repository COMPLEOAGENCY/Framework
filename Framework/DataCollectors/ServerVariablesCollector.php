<?php

namespace Framework\DataCollectors;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

class ServerVariablesCollector extends DataCollector implements Renderable
{
    public function collect()
    {
        return $_SERVER;
    }

    public function getName()
    {
        return 'server';
    }

    public function getWidgets()
    {
        return [
            "Server Variables" => [
                "icon" => "server",
                "widget" => "PhpDebugBar.Widgets.VariableListWidget",
                "map" => "server",
                "default" => "{}"
            ]
        ];
    }
}
