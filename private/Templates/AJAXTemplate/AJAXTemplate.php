<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Template');
import('Page.Page');
import('Ajax.AjaxRequest');

class AJAXTemplate extends Template {
    private $ajaxRequest;
    
    public function __construct($ajaxRequest) {
        if(!($ajaxRequest instanceof AjaxRequest)) throw new Exception('Invalid AjaxRequest');
        parent::__construct('AJAX', '1.00', __FILE__, strtotime('8th January 2015'));
        $this->ajaxRequest = $ajaxRequest;
    }
    
    /**
     * 
     * @return AjaxRequest
     */
    public function getAjaxRequest() {return $this->ajaxRequest;}
    
    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {
        parent::startPage($page);
    }
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {
        /*
         * Ajax Requests may be JSON or JSONP for cross server scripting, this
         * can be disabled in the config however, so make sure we are doing this
         * as securely as possible.
         */
        
        if(getconf('DEBUG_MODE')) {
            $json_encoded = json_encode($data, JSON_PRETTY_PRINT);
        } else {
            $json_encoded = json_encode($data);
        }
        
        if($this->ajaxRequest->isJSONP()) {
            $x = $this->ajaxRequest->getJSONPCallback() . '('.$json_encoded.')';
        } else {
            $x = $json_encoded;
        }
        
        return $x;
    }
}