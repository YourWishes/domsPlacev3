<?php
namespace LogsAddon;
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
import('Database.VirtualDatabase.VirtualQuery.*');
import('Database.VirtualDatabase.VirtualClass.*');

/**
 * Description of LogType
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class LogUser extends Log {
    public static function getFields() {
        $list = parent::getFields();
        $list->add(self::getTable()->getFields());
        return $list;
    }
    
    public static function getIDField() {
        return static::getTable()->getField('id');
    }
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getTable() {
        return LogsAddon::getInstance()->getLogUsersTable();
    }
    
    public static function getForLog($log) {
        if(!($log instanceof Log)) throw new \Exception("Invlaid Log");
        $query = new \GetQuery();
        $query->addFields(self::getTable()->getFields());
        $query->addClause(new \WhereClause(self::getTable()->getField('id'), \ClauseOperator::$EQUALS, $log->getID()));
        $rs = $query->fetch(self::getTable()->database);
        if($rs->size() < 1) return null;
        return static::getByEntry($rs->get(0));
    }
    
    public static function createForUser($log, $user) {
        if(!($log instanceof Log)) throw new \Exception("Invalid Log.");
        if(!($user instanceof \UsersAddon\User)) throw new Exception("Invalid User");
        
        $ulog = new LogUser($log);
        $ulog->userid = $user->getID();
        $ulog->createEntry();
        static::getTable()->database->commitChanges();
        
        return $ulog;
    }
    
    //Instance
    private $userid;
    
    public function __construct($log=null) {
        parent::__construct($log);
        if($log instanceof LogUser) {
            $this->userid = $log->userid;
        }
    }
    
    public function getUserID() {return $this->userid;}
    
    /**
     * @return \UsersAddon\User
     */
    public function getUser() {
        $user = \UsersAddon\User::getByID($this->userid);
        return $user;
    }

    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        
        if($field->getName() == 'user_id') {
            return $this->userid;
        }
        return null;
    }

    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'user_id') {
            $this->userid = intval($value);
            return true;
        }
        return false;
    }

    public function jsonSerialize() {
        $x = parent::jsonSerialize();
        $x["user_id"] = $this->userid;
        return $x;
    }
}
