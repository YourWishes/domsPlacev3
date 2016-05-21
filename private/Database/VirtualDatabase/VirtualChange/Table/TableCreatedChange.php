<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

/*
 * Copyright 2016 Dominic Masters <dominic@domsplace.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

import('Database.VirtualDatabase.VirtualChange.VirtualChangeType');
import('Database.VirtualDatabase.VirtualChange.Table.TableChange');

/**
 * Description of TableCreatedChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class TableCreatedChange extends TableChange {
    public function __construct(&$table) {
        parent::__construct($table, VirtualChangeType::$TABLE_CREATED);
    }

    public function commit($managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception();
        if($this->getTable()->getFields()->size() < 1) throw new Exception('There must be at least 1 field.');
        
        //Generate our SQL here.
        $query = "CREATE TABLE `".$this->getDatabase()->getName()."`.`" . $this->getTable()->getName() . "` (";
        
        //Add our fields in.
        $i = 0;
        $s = $this->getTable()->getFields()->size();
        foreach($this->getTable()->getFields() as $field) {
            if(!($field instanceof VirtualField)) continue;
            $query .= "`" . $field->getName() . "` ";
            $query .= $field->getFieldType()->getMySQL();
            
            if($field->max_length != -1 || is_array($field->max_length)) {
                if(is_array($field->max_length)) {
                    $query .= "(";
                    for($j = 0; $j < sizeof($field->max_length); $j++) {
                        $query .= $field->max_length[$j];
                        if($j < sizeof($field->max_length)-1) $query .= ", ";
                    }
                    $query .= ") ";
                } else {
                    $query .= "(" . $field->max_length . ") ";
                }
            } else {
                $query .= " ";
            }
            
            if(!$field->is_nullable) $query .= "NOT NULL ";
            if($field->auto_increment) $query .= "AUTO_INCREMENT ";
            //if($field->primary_key) $query .= "PRIMARY KEY ";
            if($field->unique_key) $query .= "UNIQUE ";
            
            if($i < ($s-1)) $query .= ", ";
            $i++;
        }
        
        //Add Primary Key Constraints
        $primary_keys = $this->getTable()->getFields()->filter('isPrimaryKey', true, array(), true);
        if($primary_keys->size() == 0) throw new \Exception("Missing primary key!");
        $query .= ", CONSTRAINT pk_" . $this->getTable()->getName() . " PRIMARY KEY (";
        $query .= $primary_keys->implode_by_function('getName', ', ');
        $query .= ")";
        
        //Add Foreign key constraints.
        foreach($this->getTable()->getFields() as $field) {
            if(!($field instanceof VirtualField)) continue;
            if(!($field->hasReference())) continue;
            $reference = $field->getReference();
            $query .= ", CONSTRAINT ";
            
            //Format: fk_$table$$field_$targettable$$targetfield$
            $query .= "fk_" . $this->getTable()->getName() . $field->getName();
            $query .= "_" . $reference->table->getName() . $reference->getName();
            $query .= " FOREIGN KEY (`" . $field->getName() . "`)";
            $query .= " REFERENCES `" . $reference->table->database->getName()."`.`";
            $query .= $reference->table->getName() . "`(`" . $reference->getName() . "`)";
        }
        $query .= ")";
        try {
            return $managed_connection->executeQuery($query);
        } catch(Exception $e) {
        }
    }
}
