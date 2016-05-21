<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

class Language {
    private static $LANGUAGES = array();
    
    //Instances
    public static $ENGLISH;
    
    //Statics
    public static function getByName($name) {
        foreach(Language::$LANGUAGES as $language) {
            if($language->name == $name) return $language;
        }
        return null;
    }
    
    public static function getDefault() {
        return Language::getByName(getconf('DEFAULT_SITE_LANGUAGE'));
    }
    
    //Instance
    private $name;
    private $code;
    
    public function __construct($name, $code) {
        $this->name = $name;
        $this->code = $code;
        array_push(Language::$LANGUAGES, $this);
    }
    
    public function getName() {return $this->name;}
    public function getLanguageCode() {return $this->code;}
}

//TODO: Add Multi-Language support
Language::$ENGLISH = new Language('English', 'en');