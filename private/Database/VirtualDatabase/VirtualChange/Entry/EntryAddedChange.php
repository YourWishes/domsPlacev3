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
import('Database.VirtualDatabase.VirtualChange.Entry.EntryChange');

/**
 * Description of TableCreatedChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class EntryAddedChange extends EntryChange {
    public function __construct(&$entry) {
        parent::__construct($entry, VirtualChangeType::$ENTRY_ADDED);
    }

    public function commit($managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception();
        
        $fields = new ArrayList('VirtualField');//Fields we NEED to add to the string, and in the order.
        foreach($this->getTable()->getFields() as $field) {
            if(!($field instanceof VirtualField)) continue;
            if(!($this->getEntry()->getData()->isKeySet($field))) continue;
            if($this->getEntry()->getData()->get($field) === VirtualField::$AUTO_INCREMENT) continue;
            
            $fields->add($field);
        }
        
        $query = "INSERT INTO `".$this->getDatabase()->getName()."`.`".$this->getTable()->getName()."` (";
        $i = 0;
        $s = $fields->size();
        
        foreach($fields as $field) {
            if(!($field instanceof VirtualField)) continue;
            $query .= "`" . $field->getName() . "`";
            if($i < ($s-1)) $query .= ", ";
            $i++;
        }
        
        $query .= ") VALUES (";
        $i = 0;
        
        foreach($fields as $field) {
            if(!($field instanceof VirtualField)) continue;
            $query .= ":" . $field->getName();
            if($i < ($s-1)) $query .= ", ";
            $i++;
        }
        
        $query .= ");";
        //TODO: Foreign Keys.
        
        $stmt = $managed_connection->prepare($query);
        
        foreach($fields as $field) {
            if(!($field instanceof VirtualField)) continue;
            $stmt->bindValue(':' . $field->getName(), $this->getEntry()->getData()->get($field));
        }
        $stmt->execute();
        
        //Now update keys.
        $keys = $managed_connection->getPHPDatabaseObject()->lastInsertId();
        foreach($this->getTable()->getFields()->reverse() as $field) {
            if(!($field->auto_increment)) continue;
            //TODO: Check for keys that are updated (due to unique_keys)
            
            $x = $this->getEntry();
            $x->getData()->put($field, $keys);
            break;
        }
    }
}
