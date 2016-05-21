<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Database.VirtualDatabase.VirtualTable');

/*
 * 
 */
class VirtualField implements JsonSerializable {
    /*
     * Static Methods
     */
    
    public static $AUTO_INCREMENT;
    
    //TODO: Valid Database Names
    public static function isValidFieldName($name) {
        return true;
    }
    
    /*
     * Instance
     */
    private $name;
    private $fieldType;
    
    /* Field Definitions */
    public $is_nullable = true;
    public $default_value;
    public $auto_increment = false;
    public $max_length = -1;
    public $table;
    
    public $primary_key = false;
    public $unique_key = false;
    
    /* References */
    private $references;//Virtual Field that this references
    
    public function __construct($name, $fieldType, $params=array()) {
        if(!($fieldType instanceof VirtualFieldType)) throw new Exception('Invalid Field Type');
        if(!is_array($params)) throw new Exception('Invalid Params Type');
        if(!VirtualField::isValidFieldName($name)) throw new Exception('Invalid Field Name');
        
        $this->name = $name;
        $this->fieldType = $fieldType;
        
        //Extract Params
        if(isset($params["nullable"]) && is_bool($params["nullable"])) {
            $this->is_nullable = $params["nullable"];
        }
        
        if(isset($params["auto_increment"]) && is_bool($params["auto_increment"])) {
            $this->auto_increment = $params["auto_increment"];
        }
        
        if(isset($params["primary_key"]) && is_bool($params["primary_key"])) {
            $this->primary_key = $params["primary_key"];
        } else {
            $this->primary_key = false;
        }
        
        if(isset($params["unique_key"]) && is_bool($params["unique_key"])) {
            $this->unique_key = $params["unique_key"];
        }
        
        if(isset($params["default"])) {
            $this->default_value = $params["default"];
        }
        
        if(isset($params["max_length"]) && is_int($params["max_length"]) && $params['max_length'] > 0) {
            $this->max_length = $params["max_length"];
        } else {
            $this->max_length = $fieldType->getMaxLength();
        }
        
        if(isset($params["reference"]) && ($params["reference"] instanceof VirtualField || is_array($params["reference"]) ) ) {
            $foreign = $params["reference"];
            if(
                (is_array($foreign) && !isset($foreign["table"])) ||
                ($foreign instanceof VirtualField && !isset($foreign->table))
            ) {
                throw new Exception('Referenced field must have a table');
            }
            
            $this->references = $foreign;
        }
    }
    
    public function isOptional() {
        /*
         * Is this field "Optional" ?
         * Optional Options..
         *      Can be NULLABLE in any way, except primary keys
         *      OR is a valid auto_increment field (INTEGER)
         */
        if($this->is_nullable && $this->primary_key !== true) {
            return true;
        } else if($this->default_value !== null) {
            return true;
        } else if($this->auto_increment && $this->getFieldType() == VirtualFieldType::$INTEGER) {
            return true;
        }
        return false;
    }
    
    public function getName() {return $this->name;}
    
    /**
     * 
     * @return VirtualFieldType
     */
    public function getFieldType() {return $this->fieldType;}
    
    /**
     * @return VirtualField
     */
    public function getReference() {
        if(!$this->hasReference()) throw new Exception('Has no reference');
        if($this->references instanceof VirtualField) return $this->references;
        $table = $this->references["table"];
        $field = $this->references["field"];
        $db = $this->table->database;
        if(isset($this->references["database"]) && $this->references["database"] instanceof VirtualDatabase) {
            $db = $this->references["database"];
        }
        if(!($db instanceof VirtualDatabase)) throw new Exception('No Database selected.');
        if(!($table instanceof VirtualTable)) $table = $db->getTable($table);
        if(!($table instanceof VirtualTable)) throw new Exception('Invalid table.');
        if(!($field instanceof VirtualField)) $field = $table->getField ($field);
        if(!($field instanceof VirtualField)) throw new Excpetion('Invalid field');
        return $field;
    }
    
    public function getDefaultValue() {
        /*
         * In future the field itself can have a default value, for now we'll 
         * use the field_type's default value.
         */
        if(isset($this->default_value)) return $this->default_value;
        return $this->fieldType->getDefaultValue();
    }
    
    public function hasReference() {
        return
            isset($this->references) && (
                $this->references instanceof VirtualField ||
                (
                    is_array($this->references) &&
                    $this->references["table"] &&
                    $this->references["field"]
                )
            )
        ;
    }
    
    public function isPrimaryKey() {return $this->primary_key;}
    
    public function jsonSerialize() {
        $x = array(
            "name" => $this->name,
            "type" => $this->fieldType,
        );
        if(isset($this->table)) $x["table"] = $this->table->getName();
        
        if($this->is_nullable) $x["is_nullable"] = true;
        if($this->max_length !== -1) $x["max_length"] = $this->max_length;
        if($this->hasReference()) $x["references"] = $this->getReference();
        if(isset($this->default_value)) $x["default_value"] = $this->default_value;
        if($this->auto_increment) $x["auto_increment"] = true;
        if($this->primary_key) $x["primary_key"] = true;
        
        return $x;
    }
}

/*
 * 
 */
class VirtualFieldType implements JsonSerializable {
    /*
     * Statics
     */
    private static $TYPES;
    
    public static $INTEGER;
    public static $DECIMAL;
    public static $VARCHAR;
    public static $TEXT;
    public static $DATETIME;
    public static $BOOLEAN;
    
    public static function getFieldTypeFromMySQLString($str) {
        $split = ArrayList::explode(array('(', ')'), $str);
        
        $type = $split[1][0][0];
        $max_length = -1;
        if(isset($split[1][1]) && isset($split[1][1][0])) {
            $max_length = intval($split[1][1][0]);
        }
        
        foreach(VirtualFieldType::$TYPES as $t) {
            if(!($t instanceof VirtualFieldType)) continue;
            if($t->mysql_name != $type) continue;
            $type = $t;
            break;
        }
        if(!($type instanceof VirtualFieldType)) throw new Exception('Invalid FieldType "'.$type.'"');
        
        return array(
            "type" => $type,
            "maxlength" => $max_length
        );
    }
    
    /*
     * Instance
     */
    private $type;
    private $php_type;
    private $mysql_name;
    private $default_value;
    private $max_length;
    
    public function __construct($type, $php_type, $mysql_name, $defaultValue, $max_length=-1) {
        if(!isset(VirtualFieldType::$TYPES)) VirtualFieldType::$TYPES = new ArrayList('VirtualFieldType');
        
        $this->type = $type;
        $this->php_type = $php_type;
        $this->mysql_name = $mysql_name;
        $this->default_value = $defaultValue;
        $this->max_length = $max_length;
        
        VirtualFieldType::$TYPES->add($this);
    }
    
    public function getType() {return $this->type;}
    public function getPHPType() {return $this->php_type;}
    public function getMySQL() {return $this->mysql_name;}
    public function getDefaultValue() {return $this->defalt_value;}
    public function getMaxLength() {return $this->max_length;}
    
    
    public function jsonSerialize() {
        return array(
            "name" => $this->type,
            "default" => $this->default_value
        );
    }
}

//Setup Statics
VirtualFieldType::$INTEGER = new VirtualFieldType('Integer', 'int', 'int', 0, 11);
VirtualFieldType::$DECIMAL = new VirtualFieldType('Decimal', 'decimal', 'decimal', 0, array(15, 4));
VirtualFieldType::$VARCHAR = new VirtualFieldType('Varchar', 'string', 'varchar', '', 256);
VirtualFieldType::$TEXT = new VirtualFieldType('Text', 'string', 'text', "");
VirtualFieldType::$DATETIME = new VirtualFieldType('Date', 'date', 'datetime', "");
VirtualFieldType::$BOOLEAN = new VirtualFieldType('Boolean', 'bool', 'tinyint', "0", 1);

VirtualField::$AUTO_INCREMENT = new VirtualFieldType('Auto Increment', 'int', '', -1);