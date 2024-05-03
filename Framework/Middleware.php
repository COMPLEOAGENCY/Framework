<?php

namespace Framework;

use Framework\MiddlewareEngine;
use Framework\HTTPRequest;
use Illuminate\Http\Response;

/* TODO: Maybe implements Middleware interface instead of abstract */

abstract class Middleware {
    /**
     * The entry point method of middleware class
     *
     * @param HTTPRequest $httpRequest The current HTTP request of middleware 
     * @param Response $httpResponse The current HTTP response of middleware 
     * @return Response
     */
    abstract public function handle(HTTPRequest $httpRequest, Response $httpResponse): Response;

    /**
     * The next method, to pass on the next middleware in chain
     *
     * @param HTTPRequest $httpRequest The current HTTP request of middleware chain 
     * @param Response $httpResponse The current HTTP response of middleware chain 
     * @return Response
     */
    public static function next(HTTPRequest $httpRequest, Response $httpResponse): Response {  
        return Framework::runMiddlewareChain($httpRequest, $httpResponse);
    }
}