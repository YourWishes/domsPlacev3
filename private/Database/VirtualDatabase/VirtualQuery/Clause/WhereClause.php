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
import('Database.VirtualDatabase.VirtualQuery.Clause.ClauseOperator');

/**
 * Description of WhereClause
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class WhereClause extends Clause implements JsonSerializable {
    private $where_what;
    private $whats;
    private $what;
    
    public function __construct($where_what, $whats, $what) {
        parent::__construct();
        if(!($where_what instanceof VirtualField)) throw new Exception("Invalid WhereWhat type.");
        if(!($whats instanceof ClauseOperator)) throw new Exception("Invalid Whats Type.");
        
        $this->where_what = $where_what;
        $this->whats = $whats;
        $this->what = $what;
    }
    
    public function getWhereWhat() {return $this->where_what;}
    
    /**
     * 
     * @return ClauseOperator
     */
    public function getClauseOperator() {return $this->whats;}
    public function getWhat() {return $this->what;}

    public function jsonSerialize() {
        return array(
            "whereWhat" => $this->where_what,
            "whats" => $this->whats,
            "what" => $this->what
        );
    }
}
