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

import('Database.*');
import('Database.VirtualDatabase.*');
import('Database.VirtualDatabase.VirtualQuery.*');
import('Database.VirtualDatabase.VirtualQuery.Clause.*');
import('Database.VirtualDatabase.VirtualQuery.VirtualRequest.*');

/**
 * Description of DeleteQuery
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class UpdateQuery extends VirtualQuery {
    public $pleaseDebug = false;//nuh uh, you didn't say the magic word.
    
    private $changes;
    
    public function __construct() {
        parent::__construct();
        
        $this->changes = new HashMap('VirtualField');
    }
    
    public function getChanges() {return $this->changes;}
    
    public function addChange($field, $new_value) {
        $this->changes->put($field, $new_value);
    }
    
    /**
     * 
     * @param $conn ManagedConnection
     * @return ArrayList
     */
    public function fetch($conn) {
        if($conn instanceof VirtualDatabase) $conn = $conn->getHandle();
        if(!($conn instanceof ManagedConnection)) throw new Exception('Invalid ManagedConenction.');
        
        //Build a query
        $fields = $this->changes;
        
        $tables_used = new ArrayList('VirtualTable');
        $bind_values = new ArrayList();
        
        $query = "UPDATE ";
        
        //Generate the tables from our where clauses
        $where_clauses = $this->getClauses()->createCopy()->filterByType('WhereClause');
        foreach($where_clauses as $clz) {
            if(!($clz instanceof WhereClause)) continue;
            $wherewhat = $clz->getWhereWhat();
            $what = $clz->getWhat();
            
            if($wherewhat instanceof \VirtualField && $wherewhat->table instanceof VirtualTable) {
                $tables_used->add($wherewhat->table);
            }
            
            if($what instanceof \VirtualField && $what->table instanceof VirtualTable) {
                $tables_used->add($what->table);
            }
        }
        
        //Add the required tables.
        for($i = 0; $i < $tables_used->size(); $i++) {
            $table = $tables_used[$i];
            if(!($table instanceof VirtualTable)) continue;
            $db = $table->database;
            if(!($db instanceof VirtualDatabase)) continue;
            $query .= "`" . $db->getName() . "`.`" . $table->getName() . "`";
            
            if($i < $tables_used->size()-1) {
                $query .= ", ";
            } else {
                $query .= " ";
            }
        }
        
        $query .= "SET ";
        $i = 0;
        $s = $fields->size();
        
        foreach($fields->keySet() as $key) {
            if(!($key instanceof VirtualField)) continue;
            $query .= "`" . $key->getName() . "` = ?";
            if($i < ($s-1)) $query .= ", ";
            $val = $fields->get($key);
            if($key->getFieldType() === VirtualFieldType::$DATETIME) {
                $val = VirtualDatabase::convertDateTimeToSQL($val);
            }
            $i++;
            $bind_values->add($val, true, true);
        }
        $query .= " ";
        
        //Add our where clauses
        if(!$where_clauses->isEmpty()) {
            $query .= "WHERE ";
        }
        
        
        foreach($fields as $field) {
            if(!($field instanceof VirtualField)) continue;
            $stmt->bindValue(':' . $field->getName(), $this->getEntry()->getData()->get($field));
        }
        
        for($i = 0; $i < $where_clauses->size(); $i++) {
            $clz = $where_clauses[$i];
            if(!($clz instanceof WhereClause)) continue;
            if($i > 0) $query .= "AND ";
            $ww = $clz->getWhereWhat();
            $w = $clz->getWhat();
            if(!($ww instanceof VirtualField)) continue;
            $query .= "`" . $ww->table->getName() . "`.`" . $ww->getName() . "` ";
            $query .= $clz->getClauseOperator()->getOperator() . " ";
            if($w instanceof VirtualField) {
                $query .=  "`" . $w->table->getName() . "`.`" . $w->getName() . "` ";
            } else {
                if($w instanceof \DateTime) {
                    $w = VirtualDatabase::convertDateTimeToSQL($w);
                }
                if(is_bool($w)) {
                    $w = $w ? '1' : '0';
                }
                $bind_values->add($w, false, true);//Allow Dupes.
                $query .= "? ";
            }
        }
        
        if($this->pleaseDebug) {
            echo "Test: ";
            die($query);
        }
        
        $stmt = $conn->prepare($query);
        
        //Bind Clauses
        for($i = 0; $i < $bind_values->size(); $i++) {
            //PDO starts at index 1.
            $val = $bind_values->get($i);
            $stmt->bindValue($i+1, $val);
        }
        
        $stmt->execute();
    }
}
