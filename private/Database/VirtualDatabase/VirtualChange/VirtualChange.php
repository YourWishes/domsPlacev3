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

/**
 * Description of VirtualChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
abstract class VirtualChange {
    private $db;
    private $change_type;
    
    public function __construct(&$db, &$change_type) {
        if(!($db instanceof VirtualDatabase)) throw new Exception('Invalid Database.');
        if(!($change_type instanceof VirtualChangeType)) throw new Exception('Invalid VirtualChange Type.');
        
        $this->db = $db;
        $this->change_type = $change_type;
    }
    
    /**
     * 
     * @return VirtualDatabase
     */
    public function getDatabase() {return $this->db;}
    public function getChangeType() {return $this->change_type;}
    
    abstract function commit($managed_connection);
}
