<?php

namespace Framework\Exceptions;

	class AppFolderNotFoundException extends \Exception
	{
		public function __construct($message = "No app folder has been found")
		{
			parent::__construct($message, "0003");
		}
	}
    