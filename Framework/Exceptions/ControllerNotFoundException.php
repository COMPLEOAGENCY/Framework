<?php

namespace Georges\Framework\Exceptions;

	class ControllerNotFoundException extends \Exception
	{
		public function __construct($message = "No controller has been found")
		{
			parent::__construct($message, "0003");
		}
	}
    