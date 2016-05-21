<?php

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

/**
 * Description of GetTableInformationQuery
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class GetTableInformationQuery {
    private $db;
    private $table;
    
    public function __construct($db, $table) {
        if(!($db instanceof VirtualDatabase)) throw new Exception('Invalid Database.');
        if(!($table instanceof VirtualTable)) throw new Exception('Invalid Table.');
        $this->db = $db;
        $this->table = $table;
    }
    
    public function fetch($conn) {
        if(!($conn instanceof ManagedConnection)) throw new Exception('Invalid connection.');
        $dname = $this->db->getName();
        $tname = $this->table->getName();
        
        $query = "SHOW COLUMNS FROM `$dname`.`$tname`";
        $stmt = $conn->prepare($query);
        $stmt->execute();
            
        $fk_query = "SELECT "
            . "`column_name`, "
            . "`referenced_table_schema` AS foreign_db, "
            . "`referenced_table_name` AS foreign_table, "
            . "`referenced_column_name`  AS foreign_column "
            . "FROM "
            . "`information_schema`.`KEY_COLUMN_USAGE` "
            . "WHERE "
            . "`constraint_schema` = '$dname' "
            . "AND "
            . "`table_name` = '$tname' "
            . "AND "
            . "`referenced_column_name` IS NOT NULL "
            . "ORDER BY "
            . "`column_name`"
        ;
        $fk_stmt = $conn->prepare($fk_query);
        $fk_stmt->execute();
        $foreign_keys = $fk_stmt->fetchAll();
        
        $fields = new ArrayList('VirtualField');
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $def = $row["Default"];
            $extra = $row["Extra"];
            $key = $row["Key"];
            $name = $row["Field"];
            
            $fti = VirtualFieldType::getFieldTypeFromMySQLString($row["Type"]);
            $type = $fti["type"];
            $maxlength = (isset($fti["maxlength"]) ? $fti["maxlength"] : $type->getMaxLength());
            
            $params = array();
            $params["nullable"] = $row["Null"] === "YES";
            $params["max_length"] = $maxlength;
            if(startsWith($extra, 'auto_increment')) $params["auto_increment"] = true;
            if(isset($row["Default"])) $params["default"] = $row["Default"];
            
            if($key == "PRI") $params["primary_key"] = true;
            
            //Check for foreign keys
            foreach($foreign_keys as $fk) {
                if($fk["column_name"] != $name) continue;
                
                //$remote_db = $fk["foreign_db"];
                $remote_table = $fk["foreign_table"];
                $remote_col = $fk["foreign_column"];
                
                //field needs to "future" exist 
                $params["reference"] = array(
                    "database" => $this->db,
                    "table" => $remote_table,
                    "field" => $remote_col
                );
            }
            
            //Create our field
            $fld = new VirtualField($name, $type, $params);
            $fields->add($fld);
        }
        
        return $fields;
    }
}
