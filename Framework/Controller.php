<?php

namespace  Framework;

use  Framework\Views;

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
        $this->_param=[];
    }


    public function view(string $viewName, array $_param = [])
    {
        $this->_view = new Views();
        $_param = array_merge($this->_param,$_param);
        $this->_httpResponse->setContent($this->_view->renderTemplate($viewName, $_param));
        return $this->_httpResponse;
    }

    public function render(string $content)
    {
        $this->_httpResponse->setContent($content);
        return $this->_httpResponse;
    }       

    public function addParam($name, $value)
    {
        $this->_param[$name] = $value;
    }
}