<?php

namespace Framework\Exceptions;

use Exception;

class ViewsTemplateFolderNotFoundException extends Exception {
	public function __construct($message = "No views template folder has been found") {
		parent::__construct($message, "0003");
	}
}
    