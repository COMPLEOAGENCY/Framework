<?php

namespace  Framework;

use  Framework\View;

class Controller
{

    public $_httpRequest;
    public $_httpResponse;
    public $_param;
    public static $dirName = "";

    public function __construct($httpRequest,$httpResponse)
    {
        $this->_httpRequest = $httpRequest;
        $this->_httpResponse = $httpResponse;
    }


    public static function setDirName(string $dirName)
    {
        self::$dirName = $dirName;
    }

    public static function view(string $viewName, array $_param = [])
    {
        View::renderTemplate(self::$dirName . "/$viewName.php", $_param);
    }

    public function addParam($name, $value)
    {
        $this->_param[$name] = $value;
    }
}
