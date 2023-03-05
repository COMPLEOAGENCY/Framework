<?php

namespace Georges\Framework\Exceptions;

class MiddlewareNotFoundException extends \Exception
{
    public function __construct($message = "Middelware needed not found")
    {
        parent::__construct($message, "0003");
    }
}
