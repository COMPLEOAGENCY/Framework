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
        $this->_param = [];
    }

    public function merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->merge_recursive_distinct($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    public function view(string $viewName, array $_param = [], $header = null)
    {
        $this->_view = new Views();

        $_param = $this->merge_recursive_distinct($this->_param, $_param);

        $this->_httpResponse->setContent($this->_view->renderTemplate($viewName, $_param));
        if ($header) {
            $this->_httpResponse->header('Content-Type', $header);
        }
        
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
