<?php

namespace Framework\Exceptions;

use Exception;

class AppFolderNotFoundException extends Exception {
	public function __construct($message = "No app folder has been found") {
		parent::__construct($message, "0003");
	}
}  