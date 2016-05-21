<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Database.VirtualDatabase.VirtualTable');
import('Database.VirtualDatabase.VirtualChange.Entry.EntryUpdatedChange');

class VirtualEntry implements JsonSerializable {
    
    //Instance
    private $table;
    private $data;
    private $original_data;
    
    public function __construct($table, $data) {
        if($table !== null && !($table instanceof VirtualTable)) throw new Exception ('Invalid Table');
        if(!($data instanceof HashMap)) throw new Exception('Invalid Data');
        if(!($data->isValidKeyClass('VirtualField'))) throw new Exception('Invalid HashMap Key Type');
        
        $this->table = $table;
        $this->data = $data;
    }
    
    /**
     * 
     * @return VirtualTable
     */
    public function getTable() {return $this->table;}
    
    /**
     * 
     * @return HashMap
     */
    public function getData() {return $this->data;}
    
    /**
     * Used for updating this field, creates a change to be commited later.
     * 
     * @param HashMap $map
     * @throws Exception
     * @return VirtualChange
     */
    public function updateData($map) {
        if(!($map instanceof HashMap)) throw new Exception ("Invalid HashMap");
        if($map->size() == 0) return null;
        
        if(!isset($this->original_data)) {
            $this->original_data = $this->data;
        }
        $old_data = $this->data->createCopy();
        
        //We will need to iterate.
        foreach($map->keySet() as $field) {
            if(!($field instanceof VirtualField)) throw new Exception("Invalid Field.");
            $value = $map->get($field);
            if(is_bool($value)) {//Was doing some weird stuff with booleans
                $value = $value ? '1' : '0';
            }
            
            $this->data->put($field, $value);
        }
        
        //Validate the map
        try {
            $this->getTable()->validateMap($this->data, false);
        } catch(Exception $e) {
            echo(json_encode($this->data));
            die('test ' . $e->getMessage());
            throw $e;
        }
        
        //Create our VirtualChange
        $change = new EntryUpdatedChage($this, $old_data);
        $this->getTable()->database->addChange($change);
        $event = new onVirtualChange($change);
        $result = $event->fire();
        if (isset($result) && is_bool($result) && !$result) throw new Exception();
        
        return $change;
    }

    public function jsonSerialize() {
        return $this->data;
    }
}