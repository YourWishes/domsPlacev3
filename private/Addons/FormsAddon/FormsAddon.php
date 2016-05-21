<?php
namespace FormsAddon;
if (!defined('MAIN_INCLUDED')) throw new \Exception();

class FormsAddon extends \Addon {
    public function __construct() {
        parent::__construct('FormsAddon', '1.00');
    }
    
    public function onEnable() {
        $this->import('Objects.*');
        //Add function to the native page class
        
        import('Page.Page');
        $page = \Page::getPage();
    }
}