<?php

namespace Framework;

use Framework\Exceptions\MiddlewareNotFoundException;
use Framework\Exceptions\AppFolderNotFoundException;
use Illuminate\Http\Response;

class Framework
{
    use Router, middlewareEngine;
    public $_httpRequest;
    private $_appFolder;

    public function __construct()
    {
        if(empty(self::$_appFolder)){
            throw new AppFolderNotFoundException();
        } else {
            $this->_httpRequest = new HttpRequest();
            Framework::setListRoute(self::$_appFolder);
            Framework::setMiddleware(self::$_appFolder);
        }
    }


    public function run()
    {
        if (Framework::setMiddlewareChain($this->_httpRequest)) {
            $this->runMiddlewareChain($this->_httpRequest);
        }

        $this->findRoute();
        $this->_foundRoute->run($this->_httpRequest);

        // echo '<pre>';
        // print_r($this->_foundRoute);
        // print_r(self::$middlewareChain);
        // echo '</pre>';
        return;
    }
}
