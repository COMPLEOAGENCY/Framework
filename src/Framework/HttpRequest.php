<?php

namespace Georges\Framework;

use Illuminate\Http\Request;
use Illuminate\Http\Redirect;


class HttpRequest
{

    private         $_param;
    private         $_method;
    public          $request;

    function __construct()
    {
        $this->request = Request::capture();
        $this->_method = $this->request->method();
        $this->_param = array();
        $this->bindParam();
    }

    public function getUrl()
    {
        return  $this->request->fullUrl();
    }

    public function getPath()
    {
        return '/' . ltrim($this->request->path(), "/");
    }

    public function getMethod()
    {
        return  $this->_method;
    }

    public function getParams()
    {
        return $this->_param;
    }

    public function setParams(array $params)
    {
        foreach ($params as $oneParams => $value) {
            $this->_param[$oneParams] = $value;
        }
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
                $this->_param = $this->request->query();
                break;
            case "POST":
            case "PUT":
                $this->_param = $this->request->post();
                break;
            case "ALL":
                $this->_param = $this->request->all();
                break;
        }
    }
}
