<?php

namespace Framework\Exceptions;

use Exception;

class RouteFolderNotFoundException extends Exception {
	public function __construct($message = "No route folder has been found") {
		parent::__construct($message, "0003");
	}
}
    