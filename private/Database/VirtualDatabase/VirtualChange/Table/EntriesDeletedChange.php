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
import('Database.VirtualDatabase.VirtualChange.Table.TableChange');

/**
 * Description of TableCreatedChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class EntriesDeletedChange extends TableChange {
    private $clauses;
    
    public function __construct(&$table, $entries) {
        if(!($entries instanceof ArrayList)) throw new Exception("Not a valid HashMap");
        if(!($entries->isValidClass('VirtualEntry'))) throw new Exception("HashMap type invalid.");
        parent::__construct($table, VirtualChangeType::$ENTRIES_DELETED);
        
        $clauses = new ArrayList('WhereClause');
        foreach($entries as $entry) {
            if(!($entry instanceof VirtualEntry)) continue;
            
            $data = $entry->getData();
            foreach($data->keySet() as $field) {
                if(!($field instanceof VirtualField)) continue;
                if(!($field->isPrimaryKey())) continue;
                
                $val = $data->get($field);
                
                $clause = new WhereClause($field, ClauseOperator::$EQUALS, $val);
                $clauses->add($clause);
            }
        }
        
        $this->clauses = $clauses;
    }

    public function commit($managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception();
        $del_query = new DeleteQuery($this->getTable(), $this->clauses);
        $del_query->fetch($managed_connection);
    }
}
