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
 * GetQuery can be used to retrieve (and manage) fields in a physical (or
 * virtual) database, based on search parameters.
 * 
 * An Object Oriented approach to standard SQL queries, adding fields you wish
 * to retrieve and in what format, as well as specifying restrictions.
 * 
 * This class is in early development for the time being.
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class GetQuery extends VirtualQuery {
    private $fields;
    private $requests;
    public $pleaseDebug = false;//nuh uh, you didn't say the magic word.
    
    public function __construct() {
        parent::__construct();
        $this->fields = new ArrayList('VirtualField');
        $this->requests = new ArrayList('VirtualRequest');
    }
    
    public function addField($field) {
        if(!($field instanceof VirtualField)) throw new Exception('Not a valid VirtualField.');
        if(!($field->table instanceof VirtualTable)) throw new Exception('Not a valid VirtualField (Missing table)');
        $this->fields->add($field);
    }
    
    public function addRequest($request) {
        if(!($request instanceof VirtualRequest)) throw new Exception();
        $this->requests->add($request);
    }
    
    public function addFields($fields) {
        foreach($fields as $field) {
            $this->addField($field);
        }
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
        $results = new ArrayList('VirtualEntry');
        
        //if($this->fields->size() == 0) return $results;
        
        $tables_used = new ArrayList('VirtualTable');
        
        $query = "SELECT ";
        //Fields
        for($i = 0; $i < $this->fields->size(); $i++) {
            $field = $this->fields[$i];
            if(!($field instanceof VirtualField)) continue;
            $table = $field->table;
            if(!($table instanceof VirtualTable)) continue;
            $db = $table->database;
            if(!($db instanceof VirtualDatabase)) continue;
            
            $query .= "`".$db->getName()."`.`" . $table->getName() . "`.`" . $field->getName() . "` as '$i'";
            $tables_used->add($field->table);
            
            if($i < $this->fields->size()-1) {
                $query .= ", ";
            } else {
                $query .= " ";
            }
        }
        
        //SELECT Requests
        for($i = 0; $i < $this->requests->size(); $i++){
            $request = $this->requests[$i];
            if(!($request instanceof VirtualRequest)) continue;
            if($request->getSelect() == null) continue;
            $tables_used->add($request->getRequiredTables());
            if($i != 0 || $this->fields->size() != 0) {
                $query .= ", ";
            }
            $j = $this->fields->size() + $i;
            $query .= $request->getSelect() . " as '$j'";
        }
        if($this->requests->size() > 0) $query .= " ";
        
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
        
        //Generate the tables from our order clauses
        $order_clauses = $this->getClauses()->createCopy()->filterByType('OrderClause');
        foreach($order_clauses as $clz) {
            if(!($clz instanceof OrderClause)) continue;
            $fld = $clz->getField();
            
            if($fld instanceof \VirtualField && $fld->table instanceof VirtualTable) {
                $tables_used->add($fld->table);
            }
        }
        
        //Generate the tables from our groupby clauses
        $groupby_clauses = $this->getClauses()->createCopy()->filterByType('GroupByClause');
        foreach($groupby_clauses as $clz) {
            if(!($clz instanceof GroupByClause)) continue;
            $fld = $clz->getField();
            
            if($fld instanceof \VirtualField && $fld->table instanceof VirtualTable) {
                $tables_used->add($fld->table);
            }
        }
        
        //Add the required tables.
        $query .= "FROM ";
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
        
        //Add our where clauses
        if(!$where_clauses->isEmpty()) {
            $query .= "WHERE ";
        }
        
        $bind_values = new ArrayList();
        
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
                $bind_values->add($w, false, true);//Allow Dupes.
                $query .= "? ";
            }
        }
        
        if(!$groupby_clauses->isEmpty()) {
            $query .= "GROUP BY ";
        
            for($i = 0; $i < $groupby_clauses->size(); $i++) {
                $clz = $groupby_clauses[$i];
                if(!($clz instanceof GroupByClause)) continue;
                if($i > 0) $query .= ", ";
                $field = $clz->getField();
                if(!($field instanceof VirtualField)) continue;
                $query .= "`" . $field->table->getName() . "`.`" . $field->getName() . "` ";
            }
        }
        
        //Add our order clauses
        if(!$order_clauses->isEmpty()) {
            $query .= "ORDER BY ";
        
            for($i = 0; $i < $order_clauses->size(); $i++) {
                $clz = $order_clauses[$i];
                if(!($clz instanceof OrderClause)) continue;
                if($i > 0) $query .= ", ";
                $field = $clz->getField();
                if(!($field instanceof VirtualField)) continue;
                $query .= "`" . $field->table->getName() . "`.`" . $field->getName() . "` ";
                $query .= $clz->isAscending() ? "ASC " : "DESC ";
            }
        }
        
        //And our limit clauses (Should only be 1)
        $limit_clauses = $this->getClauses()->createCopy()->filterByType('LimitClause');
        if(!$limit_clauses->isEmpty()) {
            $query .= "LIMIT ";
            $clz = $limit_clauses[0];
            if(!($clz instanceof LimitClause)) throw new Exception("Invalid Limit Clause");
            $query .= $clz->getOffset() !== null ? $clz->getOffset() . ", " : "";
            $query .= $clz->getCount();
        }
        
        $query .= ";";//And all was well once again.
        if($this->pleaseDebug) {
            die($query);
        }
        
        $stmt = $conn->prepare($query);
        
        //Bind Clauses
        for($i = 0; $i < $bind_values->size(); $i++) {
            //PDO starts at index 1.
            $val = $bind_values[$i];
            $type = PDO::PARAM_STR;
            if($val instanceof DateTime) {
                $val = VirtualDatabase::convertDateTimeToSQL($val);
            }
            $stmt->bindValue($i+1, $val);
        }
        
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data = new HashMap('VirtualField');
            $table = $tables_used[0];
            foreach($row as $k => $l) {
                $value = $l;
                
                if($k >= $this->fields->size()) {
                    $j = $k - $this->fields->size();
                    $r = $this->requests[$j];
                    
                    $field = new VirtualField($r->getName(), $r->getFieldType());
                } else {
                    $field = $this->fields[$k];
                }
                    
                if($field->getFieldType() == VirtualFieldType::$BOOLEAN) {
                    $value = intval($value) == 0 ? false : true;
                } else if($field->getFieldType() == VirtualFieldType::$DATETIME) {
                    $value = DateTime::createFromFormat("Y-m-d H:i:s", $value);
                    if($value === false) {
                        $value = null;
                    }
                }
                
                $data->put($field, $value);
            }
            
            $entry = new VirtualEntry($table, $data);
            $results->add($entry);
        }
        
        return $results;
    }
}
