<?php

namespace Georges\Framework;

class Redirect
{

    public static function route(string $path, $httpRequest, $headers = null)
    {
        $hostURL = self::_getHostUrl();
        $requestScheme = self::_getSchemeRequest();
        $params =  !empty($httpRequest->getParams()) ? "?" . http_build_query($httpRequest->getParams()) : "";

        // TODO 
        if (!headers_sent()) {
            // echo 'php redirection';
            header('Location: ' . $requestScheme . $hostURL . "/" . $_SERVER['SCRIPT_NAME'] . $path . $params, false);
        } else {
            // echo 'js redirection';
            echo '<script>window.location="' . $requestScheme . $hostURL . "/" . $_SERVER['SCRIPT_NAME'] . $path . $params . '"</script>';
        }
        exit;
    }

    public static function with($httpRequest, array $params)
    {
        $httpRequest->setParams($params);
    }

    private static function _getHostUrl()
    {
        return (!empty(@$_SERVER['HTTP_X_FORWARDED_HOST'])) ? @$_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];
    }

    private static function _getSchemeRequest()
    {
        $requestScheme = "https://";
        if (@$_SERVER['HTTP_X_FORWARDED_HOST'] == "localhost") {
            $requestScheme = "http://";
        } else if ($_SERVER['HTTP_HOST'] == "localhost") {
            $requestScheme = "http://";
        }
        return $requestScheme;
    }
}
