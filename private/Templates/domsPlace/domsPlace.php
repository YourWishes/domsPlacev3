<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Template');
import('Page.Page');

class domsPlace extends Template {
    private $header;
    private $footer;
    
    public function __construct() {
        parent::__construct('domsPlace', '1.00', __FILE__, strtotime('10 January 2016'));
    }
    
    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {
        parent::startPage($page);
        
        //Add Global Tags
        $page->getTags()->add($x = $this->getName());
        
        //Import Components
        $this->header = $this->importComponent('Header');
        $this->footer = $this->importComponent('Footer');
    }
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {
        $x = '';
        
        if(isset($this->header)) $x .= $this->header->generate($this, $page, $data);
        $x .= $data;
        if(isset($this->footer)) $x .= $this->footer->generate($this, $page, $data);
        
        return $x;
    }
    
    public function generateIcon($name, $extra_classes=array()) {
        $extra_classes = new ArrayList($extra_classes);
        return '<i class="fa fa-' . $name . ' ' .$extra_classes->implode(' ') .'"></i>';
    }
    
    public function generateInput($name, $type, $placeholder=null, $required=false, $something=null, $max_length=-1) {
        $x = '<input id="' . $name . '" name="' . $name . '" ';
        $x .= 'type="' . $type . '" ';
        if($placeholder != null) $x .= 'placeholder="' . $placeholder . '" ';
        if($required) $x .= 'required ';
        if($max_length > 0) $x .= 'maxlength="' . $max_length . '" ';
        
        $x .= 'class="form-control"';
        
        $x .= ' />';
        
        return $x;
    }
    
    public function genError($errno=-1, $errstr=null, $errfile=null, $errline=-1, $errcontext=null) {
        $str = parent::genError($errno, $errstr, $errfile, $errline, $errcontext);
        return $this->generatePanel($str, 'OH SNAP! An Error Occured!', null, array('panel-danger'));
    }
    
    public function escapeHTML($x) {
        return nl2br(htmlspecialchars($x, ENT_HTML5|ENT_COMPAT));
    }
    
    public function generateYesNoButtons($yes_url, $no_url, $yes_class='btn-success', $no_class='btn-danger') {
        $x = '<div class="btn-group">';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().$yes_url.'" class="btn '.$yes_class.'">Yes</a>';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().$no_url.'" class="btn '.$no_class.'">No</a>';
        $x .= '</div>';
        return $x;
    }
}