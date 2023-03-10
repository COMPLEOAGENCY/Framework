<?php

namespace Framework;

use Framework\Exceptions\ActionNotFoundException;
use Framework\Exceptions\ControllerNotFoundException;

class Route
{
    private $_path;
    private $_controller;
    private $_action;
    private $_method;
    private $_param;


    public function __construct($route = null)
    {
        $this->_path = $route->path;
        $this->_controller = $route->controller;
        $this->_action = $route->action;
        $this->_method = $route->method;
        $this->_param = $route->param ?? [];
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getParam()
    {
        return $this->_param;
    }

    public function run($httpRequest,$httpResponse)
    {
        $controller = null;
        $controllerName = 'Controllers\\' . $this->_controller;
        if (class_exists($controllerName)) {
            $controller = new $controllerName($httpRequest,$httpResponse);
            if (method_exists($controller, $this->_action)) {
                return $controller->{$this->_action}(array_merge($httpRequest->getParams(),$this->getParam()));
            } else {
                throw new ActionNotFoundException();
            }
        } else {
            throw new ControllerNotFoundException();
        }
    }
}