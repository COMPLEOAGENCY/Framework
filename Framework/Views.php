<?php

namespace Framework;

use Framework\Exceptions\ViewsTemplateFolderNotFoundException;
use Framework\Exceptions\ViewsCacheFolderNotFoundException;
use Framework\Exceptions\ViewNotFoundException;

use eftec\bladeone\BladeOne;
use RuntimeException;
use Throwable;

/**
 * Class Views
 *
 * @property string $templateFolder The views template folder path
 * @property string $cacheFolder The views cache folder path
 * @property BladeOne $bladeOne The instance of BladeOne
 */
class Views {
    protected static $templateFolder;
    protected static $cacheFolder;
    protected static $bladeOne;

    /**
     * Initialize Views static class
     *
     * @param string $folder The app folder path
     * @return void
     */
    public static function init(string $folder) {
        $templateFolder = realpath($folder."../template");
        $cacheFolder = realpath($folder."../cache");
        /* Check if app template folder exist */
        if(!is_dir($templateFolder)) {
            throw(new ViewsTemplateFolderNotFoundException("The views template folder doesn't exist : ".$templateFolder));
        }
        /* Check if app cache folder exist */
        else if(!is_dir($cacheFolder)) {
            throw(new ViewsCacheFolderNotFoundException("The views cache folder doesn't exist : ".$cacheFolder));
        }
        
        self::$bladeOne = new BladeOne($templateFolder, $cacheFolder, BladeOne::MODE_DEBUG);
        self::$templateFolder = $templateFolder;
        self::$cacheFolder = $cacheFolder;
    }

    /**
     * Render blade engine template 
     * 
     * @param string $template The blade template name
     * @param array $args The blade template arguments
     *
     * @return string
     */
    public static function renderTemplate(string $template, array $args = []): string {
        try {
            return self::$bladeOne->run($template, $args);
        }
        catch(RuntimeException $error) {
            if(str_starts_with($error->getMessage(), "Template not found")) {
                throw(new ViewNotFoundException("The views template doesn't exist : ".$template));
            }
            throw($error);
        }
    }
}
