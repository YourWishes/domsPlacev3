<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Event.Event');
import('Event.EventType');
import('Event.EventRegistered');

//EventListener, listens for an event of certain types.
class EventListener {
    /*
     * EventListener Matrix, event listeners MUST be registered and deregistered
     * ALSO never ever change what events an event listener is listening for.
     * 
     * Why? because it will mess with the matrix, if nothing else. The Matrix
     * has a defined rule set to make it efficient and adding/removing events
     * that an EventListener is listening for will cause something to break
     * somewhere.
     * 
     * Basic Matrix Structure:
     * +-----------------+-----------------------------------------------------+
     * |    EventType    | EventListener(s)                                    |
     * +-----------------+-----------------------------------------------------+
     * | WhateverType    | WhateverListener                                    |
     * +-----------------+-----------------------------------------------------+
     * 
     * EventsListeners can be fired based on their EventType with ease.
     */
    public static $REGISTERED_LISTENERS;
    
    //Instance
    private $eventTypes;
    
    public function __construct(&$eventTypes) {
        if(!($eventTypes instanceof EventType) && (!($eventTypes instanceof ArrayList) || $eventTypes->isValidClass('EventType'))) {
            throw new Exception('Invalid EventType(s)');
        }
        
        if($eventTypes instanceof EventType) {
            $this->eventTypes = new ArrayList('EventType');
            $this->eventTypes->add($eventTypes);
        } else {
            $this->eventTypes = $eventTypes;
        }
    }
    
    public function register() {
        if(!isset(EventListener::$REGISTERED_LISTENERS)) {
            EventListener::$REGISTERED_LISTENERS = new ArrayList('EventRegistered');
        }
        if(!(EventListener::$REGISTERED_LISTENERS instanceof ArrayList)) {
            throw new Exception();
        }
        
        
        //First, we need to iterate over each EventType we're listening for
        foreach($this->eventTypes as $eventType) {
            //Do we need to make the sub array?
            $r = EventListener::$REGISTERED_LISTENERS->getByFunctionValue('getEventType', $eventType, array(), true);
            if($r == null) {
                $r = new EventRegistered($eventType);
            }
            
            $r->register($this);
            
            EventListener::$REGISTERED_LISTENERS->add($r);
        }
    }
    
    public function deregister() {
        if(!isset(EventListener::$REGISTERED_LISTENERS)) return;
        
        $arr = EventListener::$REGISTERED_LISTENERS;
        if(!($arr instanceof ArrayList)) throw new Exception();
        
        //First, we need to iterate over each EventType we're listening for
        foreach($this->eventTypes as $eventType) {
            //Do we need to make the sub array?
            $r = $arr->getByFunctionValue('getEventType', $eventType, true);
            if($r === null || !($r instanceof EventRegistered)) continue;
            $r->deregister($this);
        }
    }
    
    /**
     * 
     * @return ArrayList
     */
    public function getEventTypes() {return $this->eventTypes->createCopy();}
    
    /**
     * On Event recieved. This function will execute when the event is fired.
     * Any superclasses must, or at least should, override this function to
     * handle the event.
     * 
     * @param Event $event
     */
    public function onEvent(&$event) {
        //Handle your event in your superclass here.
    }
}