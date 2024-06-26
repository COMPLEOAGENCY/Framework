<?php

namespace Framework;

use Framework\Enums\HTTPMethod;
use Illuminate\Http\Request;

class HttpRequest
{
    private $_param = [];
    private $_method;
    private $_route;
    public $request;
    public $path;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->_method = HTTPMethod::fromValue($this->request->method());
        $this->path = '/' . ltrim($this->request->getPathInfo(), "/");
        $this->bindParam();
    }

    public function getUrl(): string
    {
        return $this->getScheme() . '://' . $this->getHost() . $this->getPath();
    }

    public function getFullUrl(): string
    {
        return  trim($this->getUrl()."?".http_build_query($this->query()),"?"); 
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = "/" . ltrim($path, "/");
    }

    public function getMethod(): string
    {
        return $this->_method->getName();
    }

    public function getHost(): string
    {
        return $this->request->header('X-Forwarded-Host', $this->request->getHost());
    }

    public function getScheme(): string
    {
        // proxy redirection detection in header
        if($this->request->header('X-HTTPS') == '1'){
            return 'https';
        }
        // check server variable for https redirection
        if ($this->request->server('HTTP_X_HTTPS') == '1') {
            return 'https';
        }
        // Méthode de secours
        return $this->request->isSecure() ? 'https' : 'http';
    }
    
    
    
    
    public function getParam(string $paramName)
    {
        return $this->_param[$paramName] ?? null;
    }

    public function getParams(): array
    {
        return $this->_param;
    }

    public function getRoute()
    {
        return $this->_route;
    }

    public function setParams(array $params): void
    {
        $this->_param = array_merge($this->_param, $params);
    }

    public function deleteParam(string $name): void
    {
        unset($this->_param[$name]);
    }

    public function setParam(string $name, $value): void
    {
        $this->_param[$name] = $value;
    }

    public function startSession()
    {
        return SessionHandler::getInstance()->startSession();
    }

    public function getSession()
    {
        return SessionHandler::getInstance()->getSession();
    }

    public function setRoute($route): void
    {
        $this->_route = $route;
    }

    private function bindParam(string $method = "ALL"): void
    {
        $params = [];
        switch (strtoupper($method)) {
            case "GET":
            case "DELETE":
                $params = $this->request->query();
                break;
            case "POST":
            case "PUT":
                $params = $this->request->post();
                break;
            case "ALL":
            default:
                $params = $this->request->all();
                break;
        }
        $this->_param = array_merge($this->_param, $params);
        unset($this->_param['query']);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->request, $method)) {
            return $this->request->{$method}(...$args);
        }
        return false;
    }
}

