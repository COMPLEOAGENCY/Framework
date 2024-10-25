<?php

namespace Framework;

use Framework\Exceptions\ActionNotFoundException;
use Framework\Exceptions\ControllerNotFoundException;
use Framework\Exceptions\ControllerActionInvalidFormatException;

class Route
{
    private $_path;
    private $_action;
    private $_method;
    private $_param = [];
    private $_constraints = [];
    private $_originalPath;

    public function __construct() {}

    public function getPath() {
        return $this->_path;
    }

    public function setPath($path) {
        $this->_originalPath = $path;
        
        // Extract and validate parameter names
        preg_match_all('/\{([^\/]+)\}/', $path, $paramNames);
        foreach ($paramNames[1] as $param) {
            if ($this->isValidParameterName($param)) {
                $this->_param[$param] = null;
            }
        }
        
        // Convert to regex pattern
        $this->_path = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $path);
        return $this;
    }

    public function getAction() {
        return $this->_action;
    }

    public function setAction($action) {
        $this->_action = $action;
        return $this;
    }

    public function getMethod() {
        return $this->_method;
    }

    public function setMethod($method) {
        $this->_method = strtoupper($method);
        return $this;
    }

    public function setMiddleware($middlewares) {
        $frameworkInstance = new \Framework\Framework();
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        
        foreach ($middlewares as $middlewareClass) {
            if (class_exists($middlewareClass)) {
                $middlewareRegistry = new \Framework\MiddlewareRegistry();
                $middlewareRegistry->setPath($this->_path)->setMiddlewareClass($middlewareClass);
                $frameworkInstance->addMiddleware($middlewareRegistry);
            }
        }
        return $this;
    }

    public function getParam() {
        return $this->_param;
    }

    public function where($param, $pattern) {
        if (isset($this->_param[$param]) && @preg_match('/' . $pattern . '/', '') !== false) {
            $this->_constraints[$param] = $pattern;
        }
        return $this;
    }

    public function run($httpRequest, $httpResponse) {
        list($controllerName, $actionName) = $this->parseAction();
        $controllerName = 'Controllers\\' . $controllerName;
        
        $this->validateControllerName($controllerName);
        $controller = new $controllerName($httpRequest, $httpResponse);
        
        $this->validateActionName($controller, $actionName);

        $mergedParams = array_merge($httpRequest->getParams(), $this->getParam());
        
        // Build regex with constraints
        $pathPattern = $this->_path;
        foreach ($this->_param as $param => $value) {
            if (isset($this->_constraints[$param])) {
                $pathPattern = preg_replace('/\(\[\^\/\]\+\)/', "({$this->_constraints[$param]})", $pathPattern, 1);
            }
        }

        // Capture and assign parameters
        if (preg_match("#^" . $pathPattern . "$#", $httpRequest->getPath(), $matches)) {
            array_shift($matches);
            $paramNames = array_keys($this->_param);
            foreach ($matches as $i => $value) {
                if (isset($paramNames[$i])) {
                    $mergedParams[$paramNames[$i]] = $this->sanitizeValue($value);
                }
            }
        }

        $httpRequest->setParams($mergedParams);

        return $controller->{$actionName}($mergedParams);
    }

    private function parseAction() {
        if (!is_string($this->_action) || strpos($this->_action, '@') === false) {
            throw new ControllerActionInvalidFormatException(
                "Controller action '{$this->_action}' is not in the correct format."
            );
        }
        return explode('@', $this->_action);
    }

    private function validateControllerName($controllerName) {
        if (!class_exists($controllerName)) {
            throw new ControllerNotFoundException("Controller {$controllerName} not found.");
        }
    }

    private function validateActionName($controller, $actionName) {
        if (!method_exists($controller, $actionName)) {
            throw new ActionNotFoundException("Action {$actionName} not found in controller.");
        }
    }

    private function isValidParameterName($name) {
        return filter_var($name, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => "/^[a-zA-Z][a-zA-Z0-9_]{0,31}$/"]]);
    }

    private function sanitizeValue($value) {
        return is_string($value) ? htmlspecialchars(trim($value)) : $value;
    }
}
