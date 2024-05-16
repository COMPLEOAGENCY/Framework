<?php

namespace Framework;

use Framework\Enums\HTTPMethod;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

class HttpRequest
{
    private $_param;
    private $_method;
    private $_route;
    public $_session;
    public $request;
    public $path;

    function __construct()
    {
        $this->request = Request::capture();
        $this->_method = HTTPMethod::fromValue($this->request->method());
        $this->_param = array();
        $this->_session = null;
        $this->path = '/' . ltrim($this->request->getPathInfo(), "/");
        $this->bindParam();
    }

    public function getUrl()
    {
        return $this->request->getScheme() . '://' . $this->getHost() . $this->getPath();
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = "/" . ltrim($path, "/");
    }

    public function getMethod()
    {
        return $this->_method->getName();
    }

    public function getHost()
    {
        return $this->request->header('X-Forwarded-Host', $this->request->getHost());
    }

    public function getScheme()
    {
        if ($this->request->header('X-HTTPS') === 'on' || $this->request->header('X-HTTPS') == '1') {
            return 'https';
        } elseif ($this->request->secure()) {
            return 'https';
        }
        return 'http';
    }

    public function getParam(string $paramName)
    {
        return $this->_param[$paramName] ?? null;
    }

    public function getParams()
    {
        return $this->_param;
    }

    public function getSession()
    {
        return $this->_session;
    }

    public function setSession(object $session)
    {
        $this->_session = $session;
    }

    public function startSession()
    {
        if (empty($this->_session)) {
            // Récupérer l'identifiant de session depuis les paramètres de l'URL
            $sessionId = $this->request->get('session_id');

            $redisConnection = RedisConnection::instance();
            $redis = $redisConnection->getRedis();

            if ($redis) {
                try {
                    $redisHandler = new RedisSessionHandler($redis, ['prefix' => 'session_']);
                    $storage = new NativeSessionStorage([], $redisHandler);
                    $session = new Session($storage);

                    // Si un identifiant de session est présent, le définir
                    if ($sessionId) {
                        $session->setId($sessionId);
                    }

                    $session->start();
                    $this->setSession($session);

                } catch (\Exception $e) {
                    // Utiliser le gestionnaire de sessions par défaut basé sur le système de fichiers
                    $session = new Session();
                    $session->start();
                    $this->setSession($session);
                }
            } else {
                // Utiliser le gestionnaire de sessions par défaut basé sur le système de fichiers
                $session = new Session();
                $session->start();
                $this->setSession($session);
            }
        }
        return $this->getSession();
    }

    public function getSessionHandler()
    {
        if ($this->_session) {
            $reflection = new \ReflectionClass($this->_session);
            $property = $reflection->getProperty('storage');
            $property->setAccessible(true);
            $storage = $property->getValue($this->_session);

            if ($storage instanceof NativeSessionStorage) {
                $saveHandler = $storage->getSaveHandler();

                if ($saveHandler instanceof SessionHandlerProxy) {
                    $realHandler = $saveHandler->getHandler();
                    return $realHandler;
                }

                return $saveHandler;
            }
        }
        return null;
    }

    public function setParams(array $params)
    {
        foreach ($params as $oneParam => $value) {
            $this->_param[$oneParam] = $value;
        }
    }

    public function deleteParam(string $name)
    {
        unset($this->_param[$name]);
    }

    public function setParam(string $name, $value)
    {
        $this->_param[$name] = $value;
    }

    public function setRoute($route)
    {
        $this->_route = $route;
    }

    public function bindParam($method = "ALL")
    {
        switch ($method) {
            case "GET":
            case "DELETE":
                if (!empty($this->request->query())) {
                    $this->_param = array_merge($this->_param, $this->request->query());
                }
                break;
            case "POST":
            case "PUT":
                if (!empty($this->request->post())) {
                    $this->_param = array_merge($this->_param, $this->request->post());
                }
                break;
            case "ALL":
                if (!empty($this->request->all())) {
                    $this->_param = array_merge($this->_param, $this->request->all());
                }
                break;
        }
        if (isset($this->_param['query'])) {
            unset($this->_param['query']);
        }
    }

    public function __call($method, $args)
    {
        if (method_exists($this->request, $method)) {
            return $this->request->{$method}(...$args);
        }
        return false;
    }
}
