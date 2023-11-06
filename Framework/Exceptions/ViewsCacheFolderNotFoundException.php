<?php

namespace Framework\Exceptions;

use Exception;

class ViewsCacheFolderNotFoundException extends Exception {
	public function __construct($message = "No views cache folder has been found") {
		parent::__construct($message, "0003");
	}
}
    