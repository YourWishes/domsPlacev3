<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Configuration.Configuration');

class DatabaseType {
    private static $TYPES = array();
    
    //Instances
    public static $MYSQL;
    
    //Methods
    /**
     * 
     * @param string $name
     * @return DatabaseType
     */
    public static function getByName($name) {
        $lower = strtolower($name);
        foreach(DatabaseType::$TYPES as $type) {
            if(strtolower($type->getName()) == $lower) return $type;
        }
        return null;
    }
    
    /**
     * 
     * @return DatabaseType
     */
    public static function getDefault() {
        return DatabaseType::getByName(getconf('DEFAULT_DATABASE_TYPE'));
    }
    
    //Instance
    private $name;
    private $pdo;
    
    public function __construct($name, $pdo) {
        $this->name = $name;
        $this->pdo = $pdo;
        array_push(DatabaseType::$TYPES, $this);
    }
    
    public function getName() {return $this->name;}
    public function getPDO() {return $this->pdo;}
}
DatabaseType::$MYSQL = new DatabaseType('MySQL', 'mysql');