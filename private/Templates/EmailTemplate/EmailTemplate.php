<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Template');
import('Page.Page');
import('Style.Styleset');
import('Style.StylesetType');
import('Email.Email');

class EmailTemplate extends Template {
    
    public function __construct() {
        parent::__construct('EmailTemplate', '1.00', __FILE__, strtotime('27th January 2015'));
    }
    
    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {
        if(!($page instanceof Email)) throw new Exception('Not an email.');
        parent::startPage($page);
    }
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {
        if(!($page instanceof Email)) throw new Exception('Not an email.');
        
        $x = '<!DOCTYPE HTML>';
        $x .= '<html lang="en">';
        $x .= '<head>';
        $x .= '<title>' . $page->getSubject() . '</title>';
        $x .= '</head>';
        
        $x .= '<body>';
        
        $x .= $data;
        
        $x .= '</body>';
        $x .= '</html>';
        
        return $x;
    }
}