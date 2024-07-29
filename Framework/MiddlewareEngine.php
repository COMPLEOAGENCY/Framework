<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\MiddlewareRegistry;
use Framework\DebugBar;

trait middlewareEngine
{

    public static $listMiddleware = [];
    public static $middlewareChain = [];
    public static $middlewareDone = [];

    public function addMiddleware($middleware)
    {
        if ($middleware instanceof MiddlewareRegistry && !$this->isMiddlewareExist($middleware)) {
            self::$listMiddleware[] = $middleware;
        }
        return $this;
    }

    public function isMiddlewareExist($middleware)
    {
        foreach (self::$listMiddleware as $existingMiddleware) {
            if ($existingMiddleware->getMiddlewareClass() === $middleware->getMiddlewareClass() && $existingMiddleware->getPath() === $middleware->getPath()) {
                return true;
            }
        }
        return false;
    }

    public function use($path, $middlewares)
    {
        $middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
        foreach ($middlewares as $middlewareClass) {
            $middleware = new MiddlewareRegistry();
            $middleware->setPath($path)->setMiddlewareClass($middlewareClass);
            $this->addMiddleware($middleware);
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
    
            if ($pathMatch && !self::isMiddlewareInChain($middlewareClass, $middlewareChain)) {
                $middlewareChain[] = ["middleware" => $middlewareClass];
            }
        }
    
        self::$middlewareChain = $middlewareChain;
        return !empty($middlewareChain);
    }
    
    public static function isMiddlewareInChain($middlewareClass, $middlewareChain)
    {
        foreach ($middlewareChain as $middleware) {
            if ($middleware['middleware'] === $middlewareClass) {
                return true;
            }
        }
        return false;
    }   


    public static function runMiddlewareChain($httpRequest, $httpResponse)
    {
        if (empty(self::$middlewareChain)) {
            return $httpResponse;
        }
    
        while (!empty(self::$middlewareChain)) {
            $firstMiddlewareInfo = array_shift(self::$middlewareChain);
            $middlewareClass = $firstMiddlewareInfo['middleware'] ?? null;
    
            if (!$middlewareClass) {
                continue;
            }
    
            // Vérifier si le middleware a déjà été exécuté
            if (in_array($middlewareClass, self::$middlewareDone, true)) {
                continue;
            }
    
            $middlewareInstance = new $middlewareClass();
            if (!method_exists($middlewareInstance, 'handle')) {
                throw new MiddlewareNotFoundException("Middleware class '{$middlewareClass}' does not have a handle method.");
            }
    
            if (DebugBar::isSet()) {
                $debugbar = DebugBar::Instance()->getDebugBar();
                // check if isset Middleware in debug bar
                if ($debugbar['time']->hasStartedMeasure('Middleware')) {
                    $debugbar['time']->stopMeasure('Middleware');
                }
                $debugbar['time']->startMeasure('Middleware', 'Middleware ' . $middlewareClass);
            }
    
            self::$middlewareDone[] = $middlewareClass;
    
            $httpResponse = $middlewareInstance->handle($httpRequest, $httpResponse);
        }
    
        return $httpResponse;
    }    
}
