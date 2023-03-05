<?php

namespace  Georges\Framework;

use  Georges\Framework\View;

class Controller
{

    private $_httpRequest;
    private $_param;
    public static $dirName = "";

    public function __construct($httpRequest)
    {
        $this->_httpRequest = $httpRequest;
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
