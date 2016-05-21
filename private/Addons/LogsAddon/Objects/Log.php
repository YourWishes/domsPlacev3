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
class Log extends \VirtualClass implements \JsonSerializable {
    public static function getFields() {
        $list = parent::getFields();
        $list->add(self::getTable()->getFields());
        return $list;
    }
    
    public static function getTable() {
        return LogsAddon::getInstance()->getLogsTable();
    }
    
    public static function getIDField() {
        return self::getTable()->getField('id');
    }
    
    public static function createLogFromType($type) {
        if(!($type instanceof LogType)) throw new Exception("Invalid type");
        $log = new Log();
        $log->time = new \DateTime();
        $log->logtypeid = $type->getID();
        $ventry = $log->createEntry();
        self::getTable()->database->commitChanges();//Must commit.
        $log->id = intval($ventry->getData()->getComp(self::getIDField()));
        
        //Now check for if we have the users addon.
        if(class_exists('\UsersAddon\User')) {
            $user = \UsersAddon\User::getLoggedInUser();
            if($user !== null) {
                //OK, a user exists, let's link.
                $ulog = LogUser::createForUser($log, $user);
            }
        }
        
        /**
         * Try Log IP
         */
        $iplog = IPLog::createForCurrent($log);
        
        return $log;
    }
    
    public function generateAllQuery() {
        $classes = getSuperClassesOf(get_called_class());
        
        $idflds = new \ArrayList('\VirtualField');
        
        foreach($classes as $class) {
            if(!is_subclass_of($class, '\LogsAddon\Log')) continue;
            $fld = $class::getIDField();
            $idflds->add($fld);
        }
    }
    
    /**
     * 
     * @return \VirtualField
     */
    public static function getTimeField() {
       return parent::getTable()->getField('date'); 
    }
    
    /**
     * 
     * @param mixed $id
     * @return VirtualClass
     */
    public static function getByID($id) {
        $id = intval($id);
        
        $id_field = self::getIDField();
        $o = self::getByFieldValue($id_field, $id, self::getFields());
        $o = new Log($o);
        $o->updateLastVersion(self::getFields());
        
        return $o;
    }
    
    //Instance
    private $id;
    private $time;
    private $logtypeid;
    
    public function __construct($lt=null) {
        if($lt instanceof Log) {
            $this->id = $lt->id;
            $this->time = $lt->time;
            $this->logtypeid = $lt->logtypeid;
        } 
    }
    
    public function getID() {return $this->id;}
    /**
     * 
     * @return \DateTime
     */
    public function getTime() {return $this->time;}
    public function getLogTypeID() {return $this->logtypeid;}
    
    /**
     * 
     * @return LogType
     */
    public function getLogType(){return LogType::getByID($this->logtypeid);}

    public function getValue($field) {
        if($field->getName() == 'id') {
            return $this->id;
        } else if($field->getName() == 'date') {
            return $this->time;
        } else if($field->getName() == 'log_type_id') {
            return $this->logtypeid;
        }
        return null;
    }

    public function setField($field, $value) {
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'date') {
            $this->time = $value;
            return true;
        } else if($field->getName() == 'log_type_id') {
            $this->logtypeid = intval($value);
            return true;
        }
        return false;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id,
            "date" => $this->time,
            "type" => $this->logtypeid,
            "time" => $this->getTime()->getTimestamp()*1000
        );
    }

}
