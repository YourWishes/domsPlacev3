<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

/**
 * CLASS IS UNFINISHED AND UNSTABLE!
 */
class Locale {
    //Constants
    private static $LOCALES;
    
    public static $EN_US;
    public static $EN_AU;
    
    //Static Methods
    
    //Instance
    private $id;
    private $name;
    private $code;
    private $primaryLanguages;
    
    public function __construct($id, $name, $code, $primaryLanguages) {
        //Validation
        if(!is_int($id)) throw new Exception('ID is invalid.');
        if(!is_string($name)) throw new Exception('Name is invalid.');
        if(!is_string($code)) throw new Exception('Locale Code invalid');
        if(!($primaryLanguages instanceof ArrayList) && !($primaryLanguages instanceof Language)) throw new Exception('Language(s) is invalid.');
        
        //Primary language MUST be an array list of Language Objects.
        if(!($primaryLanguages instanceof ArrayList)) {
            $x = new ArrayList('Language');
            $x->add($primaryLanguages);
            $primaryLanguages = $x;
        } else if(!$primaryLanguages->isValidClass('Language')) {
            throw new Exception('Primary Languages must be a valid ArrayList of Language Objects.');
        }
        
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->primaryLanguages = $primaryLanguages;
        
        if(!isset(Locale::$LOCALES)) Locale::$LOCALES = new ArrayList('Locale');
        
        Locale::$LOCALES->add($this);
    }
    
    public function getID() {return $this->id;}
    public function getName() {return $this->name;}
    public function getCode() {return $this->code;}
    
    /**
     * 
     * @return ArrayList List of primarily spoken languages for this locale.
     */
    public function getPrimaryLanguages() {return $this->primaryLanguages;}
}

//Setup Statics
Locale::$EN_US = new Locale(0, 'Unitied States', 'en-US');