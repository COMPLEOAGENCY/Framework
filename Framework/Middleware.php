<?php

namespace Framework;

use Framework\MiddlewareEngine;
use Framework\HttpRequest;
use Framework\HttpResponse;


/* TODO: Maybe implements Middleware interface instead of abstract */

abstract class Middleware {
    /**
     * The entry point method of middleware class
     *
     * @param HttpRequest $httpRequest The current HTTP request of middleware 
     * @param HttpResponse $httpResponse The current HTTP response of middleware 
     * @return HttpResponse
     */
    abstract public function handle(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse;

    /**
     * The next method, to pass on the next middleware in chain
     *
     * @param HttpRequest $httpRequest The current HTTP request of middleware chain 
     * @param HttpResponse $httpResponse The current HTTP response of middleware chain 
     * @return HttpResponse
     */
    public static function next(HttpRequest $httpRequest, HttpResponse $httpResponse): HttpResponse {  
    
        return Framework::runMiddlewareChain($httpRequest, $httpResponse);

    }
}