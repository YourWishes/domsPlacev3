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

import('Database.VirtualDatabase.VirtualQuery.VirtualRequest.VirtualRequest');

/**
 * Description of Request
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class CountRequest extends VirtualRequest {
    private $field;
    
    public function __construct($field) {
        if(!($field instanceof VirtualField)) throw new Exception("Not a valid VirtualField");
        $this->field = $field;
    }
    
    /**
     * 
     * @return VirtualField
     */
    public function getField() {return $this->field;}
    
    public function getSelect() {
        return 'COUNT(`' . $this->getField()->table->database->getName() . "`.`" .
            $this->getField()->table->getName() . '`.`' . $this->getField()->getName() . '`)';
    }

    public function getRequiredTables() {
        $x = new ArrayList('VirtualTable');
        $x->add($this->getField()->table);
        return $x;
    }

    public function getName() {return "COUNT";}

    public function getFieldType() {
        return VirtualFieldType::$INTEGER;
    }
}
