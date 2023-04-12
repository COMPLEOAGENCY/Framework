<?php

namespace  Framework;

use  Framework\View;

class Controller
{

    public $_httpRequest;
    public $_httpResponse;
    public $_view;
    public $_param;

    public function __construct($httpRequest, $httpResponse)
    {
        $this->_httpRequest = $httpRequest;
        $this->_httpResponse = $httpResponse;
    }


    public function view(string $viewName, array $_param = [])
    {
        $this->_view = new View();
        echo $this->_view->renderTemplate($viewName, $_param);
    }

    public function addParam($name, $value)
    {
        $this->_param[$name] = $value;
    }
}
