<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Event.EventType');
import('Event.EventListener');

//Simple Event that can be fired.
class EventRegistered {
    
    //Instance
    private $eventType;
    private $registered;
    
    public function __construct(&$eventType) {
        if(!($eventType instanceof EventType)) throw new Exception('Invalid EventType.');
        $this->eventType = $eventType;
        $this->registered = new ArrayList('EventListener');
    }
    
    /**
     * 
     * @return EventType
     */
    public function getEventType() {return $this->eventType;}
    
    public function register(&$listener) {
        $this->registered->add($listener);
    }
    
    public function deregister(&$listener) {
        $this->registered->remove($listener);
    }
    
    public function deregisterAll() {
        $this->registered = new ArrayList('EventListener');
    }
    
    public function fire(&$event) {
        if(!($event instanceof Event)) return;
        
        foreach($this->registered as $listener) {
            if(!($listener instanceof EventListener)) continue;
            $listener->onEvent($event);
        }
    }
}