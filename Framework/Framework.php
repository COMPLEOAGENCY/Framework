<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\Exceptions\AppFolderNotFoundException;
use Illuminate\Http\Response as Response;

class Framework
{
    use Router, MiddlewareEngine;
    public $request;
    public $response;
    public static $appFolder;

    public function __construct()
    {
        if(empty(self::$appFolder)){
            throw new AppFolderNotFoundException();
        }

        $this->request = new HttpRequest();
        $this->response = new Response();

    }

    public static function setAppFolder($folder){
        self::$appFolder = $folder;
    }

    public static function getAppFolder(){
        return self::$appFolder;
    }

    public function run()
    {

        /* find route for current request */
        $this->findRoute();

        /* set middleware for current request */
        self::setMiddleware(self::$appFolder);
        self::setMiddlewareChain($this->request);  

        /* run middleware chain */      
        $this->response = $this->runMiddlewareChain($this->request,$this->response );

        /* run route */
        $this->response = $this->_foundRoute->run($this->request,$this->response);

        if(!$this->response instanceof Response){
            $content = $this->response;
            $this->response = new Response();
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