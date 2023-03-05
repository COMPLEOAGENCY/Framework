<?php

namespace Framework;


class Middleware{

    public static function next($httpRequest){  
        return Framework::runMiddlewareChain($httpRequest);
    }



}