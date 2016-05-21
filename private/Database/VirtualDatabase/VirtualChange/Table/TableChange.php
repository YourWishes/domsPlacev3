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
import('Database.VirtualDatabase.VirtualChange.VirtualChange');

/**
 * Description of TableChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
abstract class TableChange extends VirtualChange {
    private $table;
    
    public function __construct(&$table, &$change_type) {
        if(!($table instanceof VirtualTable)) throw new Exception('Invalid Table Type.');
        if(!($table->database instanceof VirtualDatabase)) throw new Exception('Invalid VirtualDatabase.');
        parent::__construct($table->database, $change_type);
        $this->table = $table;
    }
    
    /**
     * 
     * @return VirtualTable
     */
    public function getTable() {return $this->table;}
}
