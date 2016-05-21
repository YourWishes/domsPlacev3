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
class LogType extends \VirtualClass {
    private static $NAME_CACHE;
    
    public static function getFields() {
        $list = parent::getFields();
        $list->add(LogsAddon::getInstance()->getLogTypesTable()->getFields());
        return $list;
    }
    
    public static function getByName($name) {
        return static::getByFieldValue(static::getTable()->getField('name'), $name);
    }
    
    public static function register($name) {
        if(!isset(LogType::$NAME_CACHE)) LogType::$NAME_CACHE = new \ArrayList('\LogsAddon\LogType');
        $o = LogType::$NAME_CACHE->getByFunctionValue('getName', $name);
        if($o instanceof LogType) return $o;
        $o = static::getByName($name);
        if($o !== null) {
            LogType::$NAME_CACHE->add($o);
            return $o;
        }
        $o = new LogType();
        $o->name = $name;
        $ventry = $o->createEntry();
        static::getTable()->database->commitChanges();
        $o->id = intval($ventry->getData()->getComp(static::getIDField()));
        LogType::$NAME_CACHE->add($o);
        
        return $o;
    }
    
    //Instance
    private $id;
    private $name;
    
    public function __construct() {}
    
    public function getID() {return $this->id;}
    public function getName() {return $this->name;}

    public function getValue($field) {
        if($field->getName() == 'id') {
            return $this->id;
        } else if($field->getName() == 'name') {
            return $this->name;
        }
        return null;
    }

    public function setField($field, $value) {
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'name') {
            $this->name = $value;
            return true;
        }
        return false;
    }
}
