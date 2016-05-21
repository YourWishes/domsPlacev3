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

/**
 * Description of DoesDatabaseExistQuery
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class DoesDatabaseExistQuery {
    private $name;
    
    public function __construct($name) {
        $this->name = $name;
    }
    
    public function fetch($conn) {
        if(!($conn instanceof ManagedConnection)) throw new Exception('Invalid ManaagedConnection.');
        
        $name = $this->name;
        $query = "SHOW DATABASES LIKE '$name';";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return sizeof($result) > 0;
    }
}
