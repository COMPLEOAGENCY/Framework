<?php

namespace Framework;
use Framework\Framework;

class Middleware{

    public static function next($httpRequest,$httpResponse){  
        return Framework::runMiddlewareChain($httpRequest,$httpResponse);
    }



}