<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Template');
import('Page.Page');

class Resume extends Template {
    public $header;
    public $footer;
    
    public function __construct() {
        parent::__construct('Resume', '1.00', __FILE__, strtotime('22nd January 2016'));
    }
    
    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {
        parent::startPage($page);
        
        //Generate Components
        $this->header = $this->importComponent('Header');
        $this->footer = $this->importComponent('Footer');
        
        //Add Global Tags
    }
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {
        $x = '';
        
        $x .= $this->header->generate($this, $page, $data);
        
        $x .= $data;
        
        $x .= $this->footer->generate($this, $page, $data);
    
        $h = strlen($x);//Asumes UTF-8...
        $h /= 1024;
        $x .= NEWLINE.'<!-- Page Size (Minus this line...) ' . $h . 'KB-->';
        
        return $x;
    }
}