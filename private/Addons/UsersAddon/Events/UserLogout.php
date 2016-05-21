<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserLogout extends \AjaxRequestHandler {
    public function __construct() {
        parent::__construct('logout');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        User::logout();
        $request->send(true);
    }
}