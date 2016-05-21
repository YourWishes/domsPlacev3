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

import('Database.VirtualDatabase.*');
import('Database.VirtualDatabase.VirtualQuery.Clause.Clause');

/**
 * Description of WhereClause
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class GroupByClause extends Clause implements JsonSerializable {
    private $field;
    
    public function __construct($field) {
        if(!($field instanceof VirtualField)) throw new \Exception("Invalid Field.");
        parent::__construct();
        $this->field = $field;
    }
    
    public function getField() {return $this->field;}

    public function jsonSerialize() {
        return array(
            "field" => $this->field
        );
    }
}
