<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Event.EventType');

class AjaxRequestEvent extends EventType {
    public static $AJAX_REQUEST_EVENT;
    
    //Instance
    public function __construct() {
        parent::__construct('AJAX Request Event', 'AjaxRequestEvent');
    }
}

AjaxRequestEvent::$AJAX_REQUEST_EVENT = new AjaxRequestEvent();