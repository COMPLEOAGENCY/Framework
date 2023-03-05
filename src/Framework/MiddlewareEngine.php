<?php
namespace Georges\Framework;
use Georges\Framework\Exceptions\MiddlewareNotFoundException;


trait middlewareEngine {

        public static $listMiddleware;
        public static $middlewareChain = [];         
    
        public static function setMiddleware() {
            $middlewareFilePath = __DIR__.'/../Config/middlewares.php';
            if(file_exists($middlewareFilePath)){                
                self::$listMiddleware = require_once($middlewareFilePath);
            }else{
                self::$listMiddleware =  [];
            }
    
        }
    
        public static function setMiddlewareChain($httpRequest) {
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

        public static function runMiddlewareChain($httpRequest){
            $cursorMiddleware    =   @self::$middlewareChain[0]['middleware'];  
            if($cursorMiddleware){
                $class  = new $cursorMiddleware();
                if (method_exists($class, 'handle')){
                    unset(self::$middlewareChain[0]);
                    self::$middlewareChain = array_values(self::$middlewareChain);
                    return $class->handle($httpRequest);
                } else {
                    throw new MiddlewareNotFoundException("Path : ".$httpRequest->getPath());
                }                
            } else {
                return;
            }

            return;
        }
    

    

}