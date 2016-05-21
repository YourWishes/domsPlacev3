<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

class CharacterSet {
    private static $TYPES = array();
    
    //Instances
    public static $UTF8;
    
    //Statics
    public static function getByName($name) {
        foreach(CharacterSet::$TYPES as $type) {
            if(strtolower($type->name) == strtolower($name)) return $type;
        }
        return null;
    }
    public static function getDefault() {return CharacterSet::getByName(getconf('SITE_CHARSET'));}
    
    //Instance
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
        
        array_push(CharacterSet::$TYPES, $this);
    }
    
    public function getName() {return $this->name;}
}

CharacterSet::$UTF8 = new CharacterSet('UTF-8');