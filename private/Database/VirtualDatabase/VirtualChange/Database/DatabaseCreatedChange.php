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
import('Database.VirtualDatabase.VirtualChange.Database.DatabaseChange');

/**
 * Description of TableCreatedChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class DatabaseCreatedChange extends DatabaseChange {
    public function __construct(&$db) {
        parent::__construct($db, VirtualChangeType::$DATABASE_CREATED);
    }

    public function commit($managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception();
        
        $query = "CREATE DATABASE `" . $this->getDatabase()->getName() . "`;";
        
        return $managed_connection->executeQuery($query);
    }
}
