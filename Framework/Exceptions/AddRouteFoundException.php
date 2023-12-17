<?php

namespace Framework\Exceptions;

use Exception;

class AddRouteFoundException extends Exception {
	public function __construct($message = "Error adding a new route") {
		parent::__construct($message, "0001");
	}
}
    