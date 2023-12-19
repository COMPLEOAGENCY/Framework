<?php

namespace Framework;
use Framework\Enums\HTTPMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Redirect;
use Symfony\Component\HttpFoundation\Session\Session;
// https://symfony.com/doc/current/session.html


class HttpRequest
{

    private         $_param;
    private         $_method;
    private         $_route;
    public          $_session;
    public          $request;
    public          $path;

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
        return  $this->request->fullUrl();
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = "/".ltrim($path,"/");
    }    

    public function getMethod()
    {
        return  $this->_method->getName();        
    }

    public function getParam(string $paramName)
    {
        if (isset($this->_param[$paramName])) {
            return $this->_param[$paramName];
        } else {
            return null;
        }
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
        if(empty($this->_session)){
            $session = new Session();
            $session->start();
            $this->setSession($session);        
        }
        return $this->getSession(); 
    }    

    public function setParams(array $params)
    {
        foreach ($params as $oneParams => $value) {
            $this->_param[$oneParams] = $value;
        }
        return;
    }

    public function deleteParam(string $name)
    {
        unset($this->_param[$name]);
        return;
    }
    public function setParam(string $name, $value)
    {
        $this->_param[$name] = $value;
        return;
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
                // $this->_param = $this->request->query();                
                if (!empty($this->request->query())) {
                    $this->_param = array_merge($this->_param, $this->request->query());
                } // hack
                break;
            case "POST":
            case "PUT":
                // $this->_param = $this->request->post();
                if (!empty($this->request->post())) {
                    $this->_param = array_merge($this->_param, $this->request->post());
                } // hack
                break;
            case "ALL":
                // $this->_param = $this->request->all();
                // var_dump( $this->_param);exit;
                if (!empty($this->request->all())) {
                    $this->_param = array_merge($this->_param, $this->request->all());
                } // hack
                break;
        }
        if (isset($this->_param['query'])) {
            unset($this->_param['query']);
        }
        // \Classes\logIt('Params when bind parameters Framework for this ' . $this->getUrl() . '', 'debug', [$this->_param, '$_REQUEST' => $_REQUEST]);
    }

    public function __call($method, $args)
    {
        if (method_exists($this->request, $method)) {
            return $this->request->{$method}(...$args);
        }
        return false;
    }
}
