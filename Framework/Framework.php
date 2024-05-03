<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\Exceptions\AppFolderNotFoundException;
use Framework\HttpResponse as HttpResponse;

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

        /* find route for current request */
        $this->findRoute();

        /* set middleware for current request */
        self::setMiddlewareChain($this->request);  

        /* run middleware chain */      
        $this->response = $this->runMiddlewareChain($this->request,$this->response );

        /* run route */
        $this->response = $this->_foundRoute->run($this->request,$this->response);

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