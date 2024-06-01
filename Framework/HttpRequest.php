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
        return $this->request->getScheme() . '://' . $this->getHost() . $this->getPath();
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
        // Liste des en-têtes à vérifier
        $headers = [
            'X-HTTPS' => 'https',
            'HTTP_X_HTTPS' => 'https',
            'X-Forwarded-Proto' => null,
            'REQUEST_SCHEME' => null
        ];
        
        // Vérifie chaque en-tête
        foreach ($headers as $header => $httpsValue) {
            $value = $this->request->header($header);
            if ($value !== null) {
                // Si l'en-tête doit être '1' pour HTTPS
                if ($httpsValue !== null && $value === '1') {
                    return 'https';
                }
                // Si l'en-tête doit être 'http' ou 'https'
                if ($httpsValue === null && ($value === 'http' || $value === 'https')) {
                    return $value;
                }
            }
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

