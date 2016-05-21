<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Page.Page');
import('Template.Component.TemplateComponent');

class TemplateComponent {
    private static $LAST_LOADED = null;
    
    /**
     * 
     * @return TemplateComponent
     */
    public static function getLastLoaded() {return TemplateComponent::$LAST_LOADED;}
    
    //Instance
    private $name;
    private $generate;
    private $construct;
    
    /**
     * 
     * @param string $name
     * @param callable $generate
     * @param callable $construct
     * @throws Exception
     */
    public function __construct($name, $generate, $construct) {
        if(!is_callable($generate)) throw new Exception('Invalid Method');
        if(!is_callable($construct)) throw new Exception('Invalid Method');
        $this->name = $name;
        $this->generate = $generate;
        $this->construct = $construct;
        
        TemplateComponent::$LAST_LOADED = $this;
    }
    
    public function getName() {return $this->name;}
    
    /**
     * 
     * @param Template $template
     * @param Page $page
     * @param string $data
     * @return mixed
     * @throws Exception
     */
    public function generate(&$template, &$page, $data) {
        if(!($template instanceof Template)) throw new Exception();
        if(!($page instanceof Page)) throw new Exception();
        $func = $this->generate;
        return $func($template, $page, $data, $this);
    }
    
    /**
     * 
     * @param Template $template
     * @return mixed
     * @throws Exception
     */
    public function construct(&$template) {
        if(!($template instanceof Template)) throw new Exception();
        $func = $this->construct;
        return $func($template);
    }
}
