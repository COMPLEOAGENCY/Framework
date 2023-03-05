<?php

namespace Georges\Framework;

use Georges\Framework\Exceptions\ViewNotFoundException;

class View
{
    protected static $startSearch = "{{";
    protected static $endSearch = "}}";
    protected static string $title = "";

    protected static array $css = [];
    protected static array $js = [];

    public static function renderTemplate(string $template, array $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    public static function getTemplate(string $template, array $args = [])
    {
        $filePath = __DIR__ . "/../Views/" . $template;

        if (file_exists($filePath)) {
            extract($args);
            ob_start();

            include($filePath);

            $content = ob_get_clean();

            $header = self::getHeader();

            //Replace {{Css}}
            $header = self::replace_asset($header, "css");

            //Replace {{Body}}
            $content = self::replace_params($content, $args);
            $header = self::replace_html($header, "body", $content);

            //Replace {{js}}
            $header = self::replace_asset($header, "js");

            return $header;
        } else {
            throw new ViewNotFoundException();
        }
    }

    public static function replace_params(string $text, array $params)
    {
        foreach ($params as $key => $value) {
            //Si Array
            if (is_array($value)) {
            }
            //Si Object
            else if (is_object($value)) {
                foreach ($value as $objectKey => $objectValue) {
                    if (is_array($objectValue)) {
                    } else {
                        $paramSearch = self::$startSearch . $objectKey . self::$endSearch;
                        $text = str_replace($paramSearch, $objectValue, $text);
                    }
                }
            }
            //Si String
            else {
                $paramSearch = self::$startSearch . $key . self::$endSearch;
                $text = str_replace($paramSearch, $value, $text);
            }
        }
        return $text;
    }

    public static function replace_html(string $page, string $balise, string $sectionHtmlToReplace)
    {
        $paramSearch = self::$startSearch . $balise . self::$endSearch;
        $page = str_replace($paramSearch, $sectionHtmlToReplace, $page);
        return $page;
    }

    public static function replace_asset(string $page, string $asset)
    {
        $paramSearch = self::$startSearch . $asset . self::$endSearch;
        if ($asset === "css") {
            $cssLink = " ";
            foreach (self::$css as $filename) {
                $cssLink .= "<style>" . file_get_contents(__DIR__ . "/../Views/css/" . $filename) . "</style>";
            }
            $page = str_replace($paramSearch, $cssLink, $page);
        } else if ($asset === "js") {
            $jsLink = " ";
            foreach (self::$js as $filename) {
                $jsLink .= "<script>" . file_get_contents(__DIR__ . "/../Views/js/" . $filename) . "</script>";;
            }
            $page = str_replace($paramSearch, $jsLink, $page);
        }

        return $page;
    }

    public static function setNameHeader($title)
    {
        self::$title = $title;
    }

    public static function getHeader()
    {
        return "<!DOCTYPE html>
                <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>" . self::$title . "</title>
                        <!-- CSS only -->
                        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi' crossorigin='anonymous'>
                        {{css}}
                    </head>
                    <body>
                        {{body}}
                         {{js}}
                         <!-- JavaScript Bundle with Popper -->
                        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js' integrity='sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3' crossorigin='anonymous'></script>
                        <script src='https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js'></script>
                        <script>
                            WebFont.load({
                                google: {
                                    families: ['Poppins']
                                }
                            });
                        </script>
                    </body>
                </html>";
    }

    public static function addCss($filename)
    {
        array_push(self::$css, $filename);
    }


    public static function addJs($filename)
    {
        array_push(self::$js, $filename);
    }
}
