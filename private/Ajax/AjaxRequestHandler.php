<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Event.Event');
import('Event.EventType');
import('Event.EventListener');
import('Ajax.AjaxRequestEvent');
import('Ajax.AjaxRequest');

//EventListener, listens for an event of certain types.
class AjaxRequestHandler extends EventListener {
    //Static
    
    /**
     * 
     * @param type $name
     * @return \AjaxRequestHandler
     */
    public static function getRequestHandler($name) {
        foreach(EventListener::$REGISTERED_LISTENERS as $listener) {
            if(!($listener instanceof AjaxRequestHandler)) continue;
            if($listener->request_listener != $name) continue;
            return $listener;
        }
        return null;
    }
    
    //Instance
    private $request_listener;
    
    public function __construct($request_listener) {
        parent::__construct(AjaxRequestEvent::$AJAX_REQUEST_EVENT);
        $this->request_listener = $request_listener;//The Request this Listener is.. listening for
    }
    
    public function getRequestListener() {return $this->request_listener;}
    
    public function onEvent(&$event) {
        parent::onEvent($event);
        
        if(!($event instanceof AjaxRequest)) return;
        if($event->getRequest() != $this->request_listener) return;
        try {
            $this->onRequest($event);
        } catch(Exception $e) {
            $event->sendError($e);
        }
    }
    
    /**
     * 
     * @param AjaxRequest $request
     */
    public function onRequest(&$request) {
        //Handle request here.
    }
}