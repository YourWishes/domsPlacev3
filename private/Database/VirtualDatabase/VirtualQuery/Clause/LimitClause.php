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
class LimitClause extends Clause implements JsonSerializable {
    private $count;
    private $offset;
    
    public function __construct($count, $offset=null) {
        parent::__construct();
        $this->count = $count;
        $this->offset = $offset;
    }
    
    public function getCount() {return $this->count;}
    public function getOffset() {return $this->offset;}

    public function jsonSerialize() {
        return array(
            "count" => $this->count,
            "offset" => $this->offset
        );
    }
}
