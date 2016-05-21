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
class PageLog extends Log {
    public static function getPageLogType() {
        return \LogsAddon\LogType::register('PageLog');
    }
    
    public static function getFields() {
        $list = parent::getFields();
        $list->add(self::getTable()->getFields());
        return $list;
    }
    
    public static function getIDField() {
        return static::getTable()->getField('id');
    }
    
    public static function getTable() {
        return LogsAddon::getInstance()->getPageLogsTable();
    }
    
    public static function create($url) {
        $log = parent::createLogFromType(static::getPageLogType());
        $plog = new PageLog($log);
        $plog->url = $url;
        $plog->createEntry();
        static::getTable()->database->commitChanges();
        return $plog;
        
    }
    
    //Instance
    private $url;
    
    public function __construct($lt=null) {
        parent::__construct($lt);
        if($lt instanceof PageLog) {
            $this->url = $lt->url;
        }
    }
    
    public function getURL() {return $this->url;}
    
    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        if($field->getName() == 'url') {
            return $this->url;
        }
        return null;
    }

    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'url') {
            $this->url = $value;
            return true;
        }
        return false;
    }
}
