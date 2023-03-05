<?php

namespace Georges\Framework\Exceptions;

	class ActionNotFoundException extends \Exception
	{
		public function __construct($message = "No action has been found")
		{
			parent::__construct($message, "0003");
		}
	}
    