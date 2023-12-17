<?php

namespace Framework;

use Framework\Exceptions\MultipleRouteFoundException;
use Framework\Exceptions\NoRouteFoundException;
use Framework\Exceptions\AddRouteFoundException;
// use Framework\Exceptions\NoControllerFoundException;
use Framework\Route;


trait Router
{
    public static $listRoute = [];
    private $_foundRoute;

    public function getListRoute()
    {
        return self::$listRoute;
    }

    public function getFoundRoute()
    {
        return $this->_foundRoute;
    }    

    public static function addRoute() {
        $route = new Route();
        self::$listRoute[] = $route;
        return $route; // Retourne la référence à l'objet dans $this->routes
    }

    public static function get($path)
    {
        return self::addRoute()->setPath($path)->setMethod('GET');
    }
    public static function post($path)
    {
        return self::addRoute()->setPath($path)->setMethod('POST');
    }
    public static function put($path)
    {
        return self::addRoute()->setPath($path)->setMethod('PUT');
    }
    public static function delete($path)
    {
        return self::addRoute()->setPath($path)->setMethod('DELETE');
    }

    public static function all(...$args)
    {
        return self::addRoute()->setPath($args[0])->setMethod('ALL');
    }

    private function findRoute()
    {
        // var_dump(self::$listRoute);die();
        $httpRequest = $this->request;
        $routeFound = array_filter(self::$listRoute, function ($route) use ($httpRequest) {
            $return = preg_match("#^" . $route->getPath() . "$#", $httpRequest->getPath()) && ($route->getMethod() == $httpRequest->getMethod() || $route->getMethod() == "ALL");
            return $return;
        });

        $numberRoute = count($routeFound);
        // if ($numberRoute > 1) {
        //     throw new MultipleRouteFoundException("Path : " . $httpRequest->getPath());
        // } else if ($numberRoute == 0) {
        if ($numberRoute == 0) {            
            throw new NoRouteFoundException("No route founded for Path : " . $httpRequest->getPath().", Method : ".$httpRequest->getMethod());
        } else {
            $route = array_shift($routeFound);
            $this->_foundRoute = $route;
        }
    }
}