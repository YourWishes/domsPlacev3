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
 * Description of GetTablesQuery
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class GetTableNamesQuery {
    //put your code here
    private $db;
    
    public function __construct($db) {
        if(!($db instanceof VirtualDatabase)) throw new Exception();
        $this->db = $db;
    }
    
    //Returns a list of table names, no other information is retrieved.
    public function fetch($managed_connection) {
        $name = $this->db->getName();
        $query = "SHOW TABLES IN `$name`";
        $stmt = $managed_connection->prepare($query);
        $stmt->execute();
        
        $list = new ArrayList();
        while($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $list->add($row[0]);
        }
        
        return $list;
    }
}
