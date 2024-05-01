<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\Exceptions\AppFolderNotFoundException;
use Framework\HttpResponse as HttpResponse;
use Framework\DebugBar;

class Framework
{
    use Router, MiddlewareEngine;
    public $request;
    public $response;
    public $started;
    public static $appFolder;

    public function __construct()
    {
        if(empty(self::$appFolder)){
            throw new AppFolderNotFoundException();
        }

        $this->request = new HttpRequest();
        $this->response = new HttpResponse();
        $this->started = microtime(true);
        if(DebugBar::isSet()){
            $debugbar = DebugBar::Instance()->getDebugBar();
            $debugbar['time']->startMeasure('framework', 'Framework');
        }

    }

    public static function setAppFolder($folder){
        self::$appFolder = $folder;
    }

    public static function getAppFolder(){
        return self::$appFolder;
    }

    public function getDuration(): float {
        return floor((microtime(true) - $this->started) * 1000);
    }

    public function run()
    {
        if(DebugBar::isSet()){
            $debugbar = DebugBar::Instance()->getDebugBar();
            $debugbar['time']->startMeasure('Route', 'Route resolution');
        }   
        /* find route for current request */
        $this->findRoute();
        if(DebugBar::isSet()){            
            $debugbar['time']->stopMeasure('Route');
        } 
        /* set middleware for current request */
        self::setMiddlewareChain($this->request);  
        if(DebugBar::isSet()){
            $debugbar = DebugBar::Instance()->getDebugBar();
            $debugbar['time']->startMeasure('Middleware Engine', 'Total Middleware Execution');
        }    
        /* run middleware chain */      
        $this->response = $this->runMiddlewareChain($this->request,$this->response );
        if(DebugBar::isSet()){            
            $debugbar['time']->stopMeasure('Middleware Engine');
            if($debugbar['time']->hasStartedMeasure('Middleware')){
                $debugbar['time']->stopMeasure('Middleware');
            }            
        } 
        /* run route */
        if(DebugBar::isSet()){
            $debugbar = DebugBar::Instance()->getDebugBar();
            $debugbar['time']->startMeasure('Route', 'Route Execution');
        }            
        $this->response = $this->_foundRoute->run($this->request,$this->response);
        if(DebugBar::isSet()){            
            $debugbar['time']->stopMeasure('Route');
        } 
        if(DebugBar::isSet()){
            $debugbar = DebugBar::Instance()->getDebugBar();
            $debugbar['time']->startMeasure('Response', 'Response Execution');
        }           
        if(!$this->response instanceof HttpResponse){
            $content = $this->response;
            $this->response = new HttpResponse();
            $this->response->setContent($content);            
        }
        // $this->response->prepare($this->request);
        // $this->response->send();
        // echo '<pre>';
        // print_r($this->_foundRoute);
        // print_r(self::$middlewareChain);
        // echo '</pre>';
        return $this->response->send();
    }
}