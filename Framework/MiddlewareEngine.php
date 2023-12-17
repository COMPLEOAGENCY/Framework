<?php
namespace Framework;
use Framework\Exceptions\MiddlewareNotFoundException;


trait middlewareEngine {

        public static $listMiddleware = [];
        public static $middlewareChain = [];         
    
        public static function setMiddleware($appFolder) {            
            $middlewareFilePath = $appFolder.'middlewares.php';            
            if(file_exists($middlewareFilePath)){
                self::$listMiddleware = require_once($middlewareFilePath);
            }    
        }

        public function getListMiddleware()
        {
            return self::$listMiddleware;
        }
        
        public function getListMiddlewareChain()
        {
            return self::$middlewareChain;
        }          
    
        public static function setMiddlewareChain($httpRequest) {
            // var_dump(self::$listMiddleware);
            $MiddlewaresFound = array_filter(self::$listMiddleware,function($middleware) use ($httpRequest){            
                $return = preg_match("#^" . $middleware['path'] . "$#", $httpRequest->getPath()) && (@$middleware['method'] == $httpRequest->getMethod() || empty($middleware['method'])) || empty($middleware['path']);            
                return $return;
            });

            $numberMiddleware = count($MiddlewaresFound);
            if($numberMiddleware > 0)
            {
                foreach($MiddlewaresFound as $MiddlewareFound){
                    if(is_array($MiddlewareFound['middleware'])){
                        foreach($MiddlewareFound['middleware'] as $v){
                            $middlewareChain[] = (["middleware"=>$v]);
                        }
                    } else {
                        $middlewareChain[]= array("middleware"=>$MiddlewareFound['middleware']);
                    }
                }

                self::$middlewareChain = $middlewareChain;
                return true;
            }
            return false;
        }  

        public static function runMiddlewareChain($httpRequest, $httpResponse) {
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
                throw new MiddlewareNotFoundException("Path: " . $httpRequest->getPath());
            }
        
            return $middlewareInstance->handle($httpRequest, $httpResponse);
        }
        

}