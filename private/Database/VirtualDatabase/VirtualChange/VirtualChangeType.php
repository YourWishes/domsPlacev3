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

import('Database.VirtualDatabase.VirtualChange.VirtualChange');

/**
 * Description of VirtualChangeType
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class VirtualChangeType {
    //Statics
    public static $ENTRY_ADDED;
    public static $ENTRY_UPDATED;
    
    public static $TABLE_CREATED;
    
    public static $DATABASE_CREATED;
    public static $DATABASE_DROPPED;
    
    public static $ENTRIES_DELETED;
    
    //Instance
    private $name;
    private $clazz;
    
    public function __construct($name, $clazz) {
        $this->name = $name;
        $this->clazz = $clazz;
    }
    
    public function getName() {return $this->name;}
    public function getClazz() {return $this->clazz;}
}

VirtualChangeType::$ENTRY_ADDED = new VirtualChangeType('Entry Added', 'EntryAddedChange');
VirtualChangeType::$ENTRY_UPDATED = new VirtualChangeType('Entry Updated', 'EntryUpdatedChange');

VirtualChangeType::$TABLE_CREATED = new VirtualChangeType('Table Created', 'TableCreatedChange');

VirtualChangeType::$DATABASE_CREATED = new VirtualChangeType('Database Created', 'DatabaseCreatedChange');
VirtualChangeType::$DATABASE_DROPPED = new VirtualChangeType('Database Dropped', 'DatabaseDroppedChange');

VirtualChangeType::$ENTRIES_DELETED = new VirtualChangeType('Entries Deleted', 'EntriesDeletedChange');
