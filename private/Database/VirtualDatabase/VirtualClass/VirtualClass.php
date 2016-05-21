<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Database.*');
import('Database.VirtualDatabase.*');
import('Database.VirtualDatabase.VirtualChange.*');
import('Database.VirtualDatabase.VirtualQuery.VirtualRequest.CountRequest.CountRequest');

/**
 * What is the VirtualClass class? VirtualClasses are objects that are stored in
 * a particular common way.
 * 
 * Usually "Classes" on a database consist of a 
 */
abstract class VirtualClass {
    private static $ID_CACHE;
    
    /**
     * 
     * @param mixed $id
     * @return VirtualClass
     */
    public static function getByID($id) {
        $id = intval($id);
        if(!isset(VirtualClass::$ID_CACHE)) {
            VirtualClass::$ID_CACHE = new HashMap(null, 'HashMap');
        }
        $cache = VirtualClass::$ID_CACHE;
        if(!($cache instanceof HashMap)) $cache = new HashMap(null, 'HashMap');
        $clazz = get_called_class();
        if($cache->isKeySet($clazz)) {
            $cache_class = $cache->get($clazz);
            if($cache_class->isKeySet($id)) {return $cache_class->get($id);}
        }
        
        $id_field = static::getIDField();
        $o = static::getByFieldValue($id_field, $id);
        
        $map;
        if($cache->isKeySet($clazz)) {
            $map = $cache->get($clazz);
        } else {
            $map = new HashMap(null, 'VirtualClass');
            if(isset($o) && $o !== null) $map->put($id, $o);
        }
        
        VirtualClass::$ID_CACHE->put($clazz, $map);
        return $o;
    }
    
    /**
     * 
     * @param VirtualEntry $virtual_entry
     * @return VirtualClass
     * @throws \Exception
     */
    public static function getByEntry($virtual_entry) {
        if(!($virtual_entry instanceof VirtualEntry)) throw new \Exception('Invalid Entry.');
        
        $data = $virtual_entry->getData();
        if(!($data instanceof \HashMap)) return null;
        
        $obj = new static();
        foreach($data->keySet() as $key) {
            $x = $obj->setField($key, $data->get($key));
            if($x !== true) {
                //throw new Exception("No value for set field '".$key->getName()."'."); Uncomment to help find query issues.
                //For the time being I thought implementing some kind of "public field" system.
                $n = $key->getName();
                $obj->{$n} = $data->get($key);
            }
        }
        
        try {
            if(isset($virtual_entry) && $virtual_entry->getTable() !== null) {
                $virtual_entry->getTable()->injectEntry($virtual_entry);//TODO: fix
            }
        } catch(Exception $e) {
            
        }
        $obj->ventry = $virtual_entry;
        
        $obj->last_version = $data;
        return $obj;
    }
    
    public static function getByFieldValue($field, $value, $fields=null) {
        if(!($field instanceof VirtualField)) throw new \Exception("Invalid FieldType.");
        
        if($fields == null) $fields = static::getFields();
        
        //Build our query
        $getQuery = new \GetQuery();
        $getQuery->addFields($fields);
        $getQuery->addClause(new \WhereClause($field, \ClauseOperator::$EQUALS, $value));
        
        /*/*Relate.
        foreach($fields as $field) {
            if(!$field->hasReference()) continue;
            $ref = $field->getReference();
            if(!$fields->contains($ref)) continue;
            $claus = new \WhereClause($field, \ClauseOperator::$EQUALS, $ref);
            $getQuery->addClause($claus);
        }*/
        
        $result = $getQuery->fetch($field->table->database);
        if($result->size() < 1) return null;
        
        
        $result = $result->get(0);
        if(!($result instanceof \VirtualEntry)) return null;
        return static::getByEntry($result);
    }
    
    public static function getAllByFieldValue($field, $value) {
        if(!($field instanceof VirtualField)) throw new \Exception("Invalid FieldType.");
        
        $fields = static::getFields();
        
        //Build our query
        $getQuery = new \GetQuery();
        $getQuery->addFields($fields);
        $getQuery->addClause(new \WhereClause($field, \ClauseOperator::$EQUALS, $value));
        
        /*/*Relate.
        foreach($fields as $field) {
            if(!$field->hasReference()) continue;
            $ref = $field->getReference();
            if(!$fields->contains($ref)) continue;
            $claus = new \WhereClause($field, \ClauseOperator::$EQUALS, $ref);
            $getQuery->addClause($claus);
        }*/
        
        $result = $getQuery->fetch($field->table->database);
        $list = new ArrayList();
        foreach($result as $r) {
            $list->add(static::getByEntry($r));
        }
        return $list;
    }
    
    /**
     * Retrieves all the instances of this VirtualClass from the database. Avoid
     * using this to decrease strain on server.
     * 
     * @return \ArrayList
     */
    public static function getAll() {
        //Avoid using
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $result = $query->fetch(static::getTable()->database);
        
        $list = new \ArrayList();
        foreach($result as $res) {
            $obj = static::getByEntry($res);
            $list->add($obj);
        }
        return $list;
    }
    
    public static function getCount($clauses=NULL) {
        $query = new \GetQuery();
        $query->addRequest(new \CountRequest(static::getIDField()));
        if($clauses instanceof ArrayList) $query->addClauses($clauses);
        $result = $query->fetch(static::getIDField()->table->database);
        if($result->size() < 1) return 0;
        $r = $result->get(0);
        if(!($r instanceof VirtualEntry)) return 0;
        $map = $r->getData();
        if(!($map instanceof HashMap) || $map->size() < 1) return 0;
        $key = $map->keySet()[0];
        $val = $map->get($key);
        return intval($r->getData()->get($key));
    }
    
    /**
     * Can be overriden.
     * @return VirtualField
     */
    public static function getIDField() {
        $fld = 'id';
        return static::getFields()->getByFunctionValue('getName',$fld);
    }
    
    /**
     * Returns a list of fields that this class needs. (Override)
     * @return \ArrayList
     */
    public static function getFields() {
        return new ArrayList('VirtualField');
    }
    
    /**
     * @return \VirtualTable
     */
    public static function getTable() {
        return static::getIDField()->table;
    }
    
    //Instance
    public $last_version;
    
    public function __construct() {}
    
    public abstract function setField($field, $value);
    public abstract function getValue($field);
    
    public function getIgnoredFields() {
        return new \ArrayList('VirtualField');
    }
    
    //Sets the "last_version" (used for updating) to be the current data... a bit tricky to work with but can help with class inheritence
    public function updateLastVersion($fields=null) {
        if($fields == null) {
            $fields = static::getFields();
        }
        $map = new HashMap('VirtualField');
        foreach($fields as $field) {
            $value = $this->getValue($field);
            if($value === null) continue;
            $map->put($field, $value);
        }
        $this->last_version = $map;
    }
    
    /**
     * 
     * @return \VirtualEntry
     * @throws \Exception
     */
    public function createEntry() {
        $ignored_fields = $this->getIgnoredFields();
        $fields = static::getFields();
        $fields->remove($ignored_fields);
        
        $table = static::getTable();
        if(!($table instanceof \VirtualTable)) throw new \Exception("Table not setup.");
        
        $null = null;
        $map = new HashMap('VirtualField');
        foreach($fields as $field) {
            $value = $this->getValue($field);
            if($value === null) continue;
            $map->put($field, $value);
        }
        
        $this->last_version = $map;
        try {
            $virtual_entry = $table->createEntry($map);
        } catch (Exception $ex) {
            throw new Exception("Cannot create entry", 0, $ex);
        }
        return $virtual_entry;
    }
    
    public function update($ventry=null) {
        if($ventry == null) {
            $ventry = $this->ventry;
        } else {
            $ventry = new VirtualEntry(static::getTable(), $this->last_version);
            static::getTable()->injectEntry($ventry);
        }
        
        //Compare...
        $prev_version = $this->last_version;
        
        $changes = new HashMap('\VirtualField');
        foreach(static::getFields() as $field) {
            if(!($field instanceof VirtualField)) continue;
            $old_value = null;
            
            
            if($prev_version instanceof HashMap && $prev_version->isKeySet($field)) {
                $old_value = $prev_version->get($field);
            }
            
            $current_value = $this->getValue($field);
            if(!($old_value == $current_value)) {
                $changes->put($field, $current_value);
            }
        }
        $change = $ventry->updateData($changes);
    }
    
    private $ventry;
    
    /**
     * So something I somewhat overlooked when making this whole VirtualClass
     * thing is how it will handle deletes. And to be honest, even now, no 
     * matter how I look at it I can't think of a better solution than:
     * 
     * override method
     * call "known" foreign key deletes
     * fire an event for "unknown" foreign key deletes.
     * call parent delete for it to do the same.
     */
    public function delete() {
        $entry = $this->ventry;
        if(!($entry instanceof VirtualEntry)) throw new \Exception("No entry.");
        if(!($entry->getTable() instanceof VirtualTable)) throw new \Exception("No table.");
        $tb = $entry->getTable();
        $tb->removeEntry($entry);
    }
}