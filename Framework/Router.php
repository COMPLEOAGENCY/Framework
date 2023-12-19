<?php

namespace Framework;

use Framework\Exceptions\MultipleRouteFoundException;
use Framework\Exceptions\NoRouteFoundException;
use Framework\Exceptions\AddRouteFoundException;
// use Framework\Exceptions\NoControllerFoundException;
use Framework\Route;

trait Router
{
    public $listRoute = []; // Changé en propriété d'instance
    private $_foundRoute;

    public function getListRoute()
    {
        return $this->listRoute;
    }

    public function getFoundRoute()
    {
        return $this->_foundRoute;
    }    

    public function addRoute() {
        $route = new Route();
        $this->listRoute[] = $route;
        return $route; // Retourne la référence à l'objet dans $this->routes
    }

    public function get($path)
    {
        return $this->addRoute()->setPath($path)->setMethod('GET');
    }
    public function post($path)
    {
        return $this->addRoute()->setPath($path)->setMethod('POST');
    }
    public function put($path)
    {
        return $this->addRoute()->setPath($path)->setMethod('PUT');
    }
    public function delete($path)
    {
        return $this->addRoute()->setPath($path)->setMethod('DELETE');
    }

    public function all(...$args)
    {
        return $this->addRoute()->setPath($args[0])->setMethod('ALL');
    }

    private function findRoute()
    {
        $httpRequest = $this->request;
        $routeFound = array_filter($this->listRoute, function ($route) use ($httpRequest) {
            return preg_match("#^" . $route->getPath() . "$#", $httpRequest->getPath()) && ($route->getMethod() == $httpRequest->getMethod() || $route->getMethod() == "ALL");
        });

        $numberRoute = count($routeFound);
        if ($numberRoute == 0) {            
            throw new NoRouteFoundException("No route founded for Path : " . $httpRequest->getPath().", Method : ".$httpRequest->getMethod());
        } else {
            $route = array_shift($routeFound);
            $this->_foundRoute = $route;
        }
    }
}
