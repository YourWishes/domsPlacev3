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
import('Database.VirtualDatabase.VirtualChange.Entry.EntryChange');

/**
 * Description of TableCreatedChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class EntryUpdatedChage extends EntryChange {
    private $old_values;
    private $clauses;
    
    public function __construct(&$entry, $old_values) {
        if(!($old_values instanceof HashMap)) throw new Exception ("Invalid HashMap");
        parent::__construct($entry, VirtualChangeType::$ENTRY_UPDATED);
        $this->old_values = $old_values;
        
        $clauses = new ArrayList('WhereClause');

        $data = $this->getEntry()->getData();
        foreach($data->keySet() as $field) {
            if(!($field instanceof VirtualField)) continue;
            if(!($field->isPrimaryKey())) continue;

            $val = $data->get($field);

            $clause = new WhereClause($field, ClauseOperator::$EQUALS, $val);
            $clauses->add($clause);
        }
        $this->clauses = $clauses;
    }

    public function commit($managed_connection) {
        if(!($managed_connection instanceof ManagedConnection)) throw new Exception();
        
        $query = new UpdateQuery();
        
        $current_values = $this->getEntry()->getData();
        foreach($this->old_values->keySet() as $key) {
            if(!($current_values->isKeySet($key))) continue;
            $oldval = $this->old_values->get($key);
            $newval = $current_values->get($key);
            if($oldval === $newval) continue;
            $query->addChange($key, $newval);
        }
        
        $query->addClauses($this->clauses);
        $query->fetch($managed_connection);
    }
}
