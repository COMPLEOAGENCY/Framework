<?php

namespace Framework;

use Framework\Exceptions\ActionNotFoundException;
use Framework\Exceptions\ControllerNotFoundException;
use Framework\Exceptions\ControllerActionInvalidFormatException;
use Framework\Middleware;
use Framework\Controller;

class Route
{
    private $_path;
    private $_action;
    private $_method;
    private $_param;


    public function __construct()
    {

    }

    public function getPath()
    {
        return $this->_path;
    }

    public function setPath($path)
    {
        $this->_path=$path;
        return $this;
    }    

    public function getAction()
    {
        return $this->_action;
    }

    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }    

    public function setMiddleware($middlewares)
    {
        $middlewareRegistry = new \Framework\MiddlewareRegistry();
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        
        foreach ($middlewares as $middlewareClass) {
            // Vérifie si le middleware existe déjà dans la liste
            $exists = false;
            foreach (\Framework\Framework::$listMiddleware as $registeredMiddleware) {
                if ($registeredMiddleware->getMiddlewareClass() === $middlewareClass) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $middlewareRegistry = $middlewareRegistry->setPath($this->_path)->setMiddlewareClass($middlewareClass);
                \Framework\Framework::$listMiddleware[] = $middlewareRegistry;
            }
        }
    
        return $this;
    }
    

    public function getMethod()
    {
        return $this->_method;
    }

    public function setMethod($method)
    {
        $this->_method=$method;
        return $this;
    }    

    public function getParams()
    {
        return $this->_param ?? [];
    }

    public function setParams($param)
    {
        $this->_param = $param ;
        return $this;
    }    

    public function run($httpRequest, $httpResponse)
    {
        list($controllerName, $actionName) = $this->parseAction();
        $controllerName = 'Controllers\\' . $controllerName;
        $this->validateControllerName($controllerName);
        $controller = new $controllerName($httpRequest, $httpResponse);

        $this->validateActionName($controller, $actionName);

        $mergedParams = array_merge($httpRequest->getParams(), $this->getParams());
        return $controller->{$actionName}($mergedParams);
    }

    private function parseAction()
    {
        if (strpos($this->_action, '@') === false) {
            throw new ControllerActionInvalidFormatException("Controller action '{$this->_action}' is not in the correct format.");
        }

        return explode('@', $this->_action);
    }

    private function validateControllerName($controllerName)
    {
        if (!class_exists($controllerName)) {
            throw new ControllerNotFoundException("Controller {$controllerName} not found.");
        }
    }

    private function validateActionName($controller, $actionName)
    {
        if (!method_exists($controller, $actionName)) {
            throw new ActionNotFoundException("Action {$actionName} not found in controller.");
        }
    }    
}