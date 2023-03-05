<?php

namespace Georges\Framework;

use Georges\Framework\Exceptions\MultipleRouteFoundException;
use Georges\Framework\Exceptions\NoRouteFoundException;


trait Router
{
    public static $listRoute;
    private $_foundRoute;

    public static function setListRoute()
    {
        $routeFilePath = __DIR__ . '/../Config/route.json';
        if (file_exists($routeFilePath)) {
            $stringRoute = file_get_contents($routeFilePath);
            self::$listRoute = json_decode($stringRoute);
        }
    }

    public function getListRoute()
    {
        return self::$listRoute;
    }

    public static function addRoute($method, $args)
    {
        $routeArray['path']            = $args[0];
        $routeArray['controller']    = $args[1];
        $routeArray['action']        = $args[2];
        $routeArray['param']        = $args[3] ?? [];
        $routeArray['method']        = $method;
        self::$listRoute[] = (object)($routeArray);
    }

    public static function get(...$args)
    {
        self::addRoute('GET', $args);
    }
    public static function post(...$args)
    {
        self::addRoute('POST', $args);
    }
    public static function put(...$args)
    {
        self::addRoute('PUT', $args);
    }
    public static function delete(...$args)
    {
        self::addRoute('DELETE', $args);
    }
    public static function all(...$args)
    {
        self::addRoute('ALL', $args);
    }

    private function findRoute()
    {
        $httpRequest = $this->_httpRequest;
        $routeFound = array_filter(self::$listRoute, function ($route) use ($httpRequest) {
            $return = preg_match("#^" . $route->path . "$#", $httpRequest->getPath()) && ($route->method == $httpRequest->getMethod() || $route->method == "ALL");
            return $return;
        });

        $numberRoute = count($routeFound);
        if ($numberRoute > 1) {
            throw new MultipleRouteFoundException("Path : " . $httpRequest->getPath());
        } else if ($numberRoute == 0) {
            throw new NoRouteFoundException("Path : " . $httpRequest->getPath());
        } else {
            $route = new Route(array_shift($routeFound));
            $this->_foundRoute = $route;
        }
    }
}
