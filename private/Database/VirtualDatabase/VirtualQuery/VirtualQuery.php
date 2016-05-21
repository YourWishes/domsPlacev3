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

import('Database.*');
import('Database.VirtualDatabase.*');
import('Database.VirtualDatabase.VirtualQuery.*');
import('Database.VirtualDatabase.VirtualQuery.Clause.*');
import('Database.VirtualDatabase.VirtualQuery.VirtualRequest.*');

/**
 * GetQuery can be used to retrieve (and manage) fields in a physical (or
 * virtual) database, based on search parameters.
 * 
 * An Object Oriented approach to standard SQL queries, adding fields you wish
 * to retrieve and in what format, as well as specifying restrictions.
 * 
 * This class is in early development for the time being.
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
abstract class VirtualQuery {
    private $clauses;
    
    public function __construct() {
        $this->clauses = new ArrayList('Clause');
    }
    
    public function addClause($clause) {
        if(!($clause instanceof Clause)) throw new Exception();
        $this->clauses->add($clause);
    }
    
    public function addClauses($clause) {$this->clauses->add($clause);}
    
    public function getClauses() {return $this->clauses->createCopy();}
    
    public abstract function fetch($conn);
}
