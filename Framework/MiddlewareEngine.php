<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\MiddlewareRegistry;

trait middlewareEngine
{

    public static $listMiddleware = [];
    public static $middlewareChain = [];
    public static $middlewareDone = [];

    public function addMiddleware()
    {
        $middleware = new MiddlewareRegistry();
        self::$listMiddleware[] = $middleware;
        return $middleware;
    }

    public function use($path, $middlewares)
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        foreach ($middlewares as $middlewareClass) {
            $this->addMiddleware()->setPath($path)->setMiddlewareClass($middlewareClass);
        }
        return $this;
    }

    public function getListMiddleware()
    {
        return self::$listMiddleware;
    }

    public function getListMiddlewareChain()
    {
        return self::$middlewareChain;
    }

    public function getListMiddlewareDone()
    {
        return self::$middlewareDone;
    }    

    public static function setMiddlewareChain($httpRequest)
    {
        $middlewareChain = [];

        foreach (self::$listMiddleware as $middlewareRegistry) {
            $path = $middlewareRegistry->getPath();
            $middlewareClass = $middlewareRegistry->getMiddlewareClass();

            $pathMatch = preg_match("#^" . $path . "$#", $httpRequest->getPath()) || empty($path);
            // Vous pouvez également ajouter une vérification pour la méthode HTTP si nécessaire

            if ($pathMatch) {
                $middlewareChain[] = ["middleware" => $middlewareClass];
            }
        }

        self::$middlewareChain = $middlewareChain;
        return !empty($middlewareChain);
    }


    public static function runMiddlewareChain($httpRequest, $httpResponse)
    {
        if (empty(self::$middlewareChain)) {
            return $httpResponse;
        }

        $firstMiddlewareInfo = array_shift(self::$middlewareChain);
        $middlewareClass = $firstMiddlewareInfo['middleware'] ?? null;

        if (!$middlewareClass) {
            return $httpResponse;
        }

        $middlewareInstance = new $middlewareClass();
        if (!method_exists($middlewareInstance, 'handle')) {
            throw new MiddlewareNotFoundException("Middleware class '{$middlewareClass}' does not have a handle method.");
        }
        self::$middlewareDone[] = $middlewareClass;

        return $middlewareInstance->handle($httpRequest, $httpResponse);
    }
}
