<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

class Configuration {
    private static $configuration;
    
    /**
     * 
     * @return Configuration
     */
    public static function getConfiguration() {
        if(isset(Configuration::$configuration)) return Configuration::$configuration;
        return Configuration::$configuration = new Configuration();
    }
    
    //Instance
    private $configurations;
    
    public function __construct() {
        $this->configurations = new ArrayList();
    }
    
    /**
     * 
     * @return array
     */
    public function getConfigurations() {return $this->configurations;}
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getConf($key) {
        try {
            $value = $this->configurations->get($key);
            return $value;
        } catch (Exception $ex) {
            return null;
        }
    }
    
    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function isConfDefined($key) {
        return $this->configurations->isKeySet($key);
    }
    
    /**
     * 
     * @param string $key
     * @param string $value
     * @throws Exception
     */
    public function setConf($key, $value) {
        if($this->configurations->isKeySet($key)) throw new Exception('Configuration already set (Cannot re-set)');
        $this->configurations->set($key, $value);
    }
    
    public function trySetConf($key, $value) {
        try {
            $this->setConf($key, $value);
            return true;
        } catch (Exception $ex) {}
        return false;
    }
}

function setconf($key, $value) {
    return Configuration::getConfiguration()->setConf($key, $value);
}

function getconf($key) {
    return Configuration::getConfiguration()->getConf($key);
}

function definedconf($key) {
    return Configuration::getConfiguration()->isConfDefined($key);
}