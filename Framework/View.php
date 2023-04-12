<?php

namespace Framework;

use Framework\Exceptions\ViewNotFoundException;
use eftec\bladeone\BladeOne;

class View
{
    public $_templateFolder;
    public $_cacheFolder;
    public $_bladeOne;

    public function __construct()
    {
        $this->setTemplateFolder(Framework::$_appFolder."../template");
        $this->setCacheFolder(Framework::$_appFolder."../cache");
        $this->_bladeOne = new BladeOne($this->_templateFolder, $this->_cacheFolder, BladeOne::MODE_DEBUG);
    }

    public function setTemplateFolder($folder)
    {
        $this->_templateFolder = $folder;
    }
    public function setCacheFolder($folder)
    {
        $this->_cacheFolder = $folder;
    }

    public function renderTemplate(string $template, array $args = [])
    {
        return $this->_bladeOne->run($template, $args);
    }
}
