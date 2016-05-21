<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Event.EventType');
import('Event.EventListener');

//Simple Event that can be fired.
class Event {
    
    //Instance
    private $eventType;
    
    public function __construct(&$eventType) {
        if(!($eventType instanceof EventType)) throw new Exception('Invalid EventType.');
        $this->eventType = $eventType;
    }
    
    /**
     * 
     * @return EventType
     */
    public function getEventType() {return $this->eventType;}
    
    public function fire() {
        //$listeners<EventRegistered>
        $listeners = EventListener::$REGISTERED_LISTENERS;
        if(!isset($listeners)) {
            return;
        }
        
        foreach($listeners as $listener) {
            if(!($listener instanceof EventRegistered)) continue;
            if($listener->getEventType() !== $this->getEventType()) continue;
            $x = $listener->fire($this);
        }
        $this->onEvent();
    }
    
    public function onEvent() {
        
    }
}