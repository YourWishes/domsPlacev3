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
abstract class EntryChange extends VirtualChange {
    private $entry;
    
    public function __construct(&$entry, &$change_type) {
        if(!($entry instanceof VirtualEntry)) throw new Exception('Invalid Entry Type.');
        if(!($entry->getTable() instanceof VirtualTable)) throw new Exception('Invalid VirtualTable.');
        parent::__construct($entry->getTable()->database, $change_type);
        
        $this->entry = $entry;
    }
    
    /**
     * 
     * @return VirtualEntry
     */
    public function getEntry() {return $this->entry;}
    
    /**
     * 
     * @return VirtualTable
     */
    public function getTable() {return $this->entry->getTable();}
}
