<?php

namespace Framework\Exceptions;

use Exception;

class MiddlewareNotFoundException extends Exception {
    public function __construct($message = "Middelware needed not found") {
        parent::__construct($message, "0003");
    }
}
