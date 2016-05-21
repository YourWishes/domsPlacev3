<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();
import('Event.Event');

/**
 * EventType is the type of event that is being fired, this can be anything from
 * an AJAX request, Database connection event or anything that an Addon uses.
 * 
 * Each Event needs to be an individual class that extends the Event class. The
 * class of said event needs to be passed into this constructor as $clazz.
 */
class EventType {
    private static $TYPES;
    
    //EventTypes
    public static $PAGE_REQUEST;
    
    //Functions
    public static function getByName($name) {
        if (!isset(self::$TYPES)) {
            return null;
        }
        $types = self::$TYPES;
        if (!($types instanceof ArrayList)) {
            return null;
        }
        return $types->getByFunctionValue('getName', $name);
    }
    
    public static function getByClass($clazz) {
        if (!isset(self::$TYPES)) {
            return null;
        }
        $types = self::$TYPES;
        if (!($types instanceof ArrayList)) {
            return null;
        }
        return $types->getByFunctionValue('getClazz', $clazz);
    }
    
    //Instance
    private $name;
    private $clazz;
    
    public function __construct($name, $clazz) {
        $this->name = $name;
        $this->clazz = $clazz;
        if(!isset(self::$TYPES)) self::$TYPES = new \ArrayList('EventType');
        self::$TYPES->add($this);
    }
    
    public function getName() {return $this->name;}
    public function getClazz() {return $this->clazz;}
}
EventType::$PAGE_REQUEST = new EventType('Page Request', 'PageRequestEvent');