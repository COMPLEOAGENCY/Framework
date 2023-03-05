<?php

namespace Georges\Framework;

use Georges\Framework\Exceptions\MiddlewareNotFoundException;
use Illuminate\Http\Response;

class Framework
{
    use Router, middlewareEngine;
    private $_httpRequest;

    public function __construct()
    {
        $this->_httpRequest = new HttpRequest();
        Framework::setListRoute();
        Framework::setMiddleware();
    }


    public function run()
    {
        if (Framework::setMiddlewareChain($this->_httpRequest)) {
            $this->runMiddlewareChain($this->_httpRequest);
        }

        $this->findRoute();
        $this->_foundRoute->run($this->_httpRequest);

        echo '<pre>';

        // print_r($this->_foundRoute);
        // print_r(self::$middlewareChain);

        echo '</pre>';
        return;
    }
}
