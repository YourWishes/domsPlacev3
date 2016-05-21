<?php
namespace domsPlaceAddon;
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
 * Description of NewsLog
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class NewsLog extends \LogsAddon\Log {
    public static function getFields() {
        $list = parent::getFields();
        $list->add(static::getTable()->getFields());
        return $list;
    }
    
    public static function getTable() {
        return domsPlaceAddon::getNewsLogsTable();
    }
    
    public static function createForPost($news, $type) {
        $log = parent::createLogFromType($type);
        return self::create($news, $log);
    }
    
    public static function create($news, $log) {
        if(!($log instanceof \LogsAddon\Log)) throw new \Exception("Invalid Log");
        if(!($news instanceof News)) throw new \Exception("Invalid News");
        $nlog = new NewsLog($log);
        $nlog->news_id = $news->getID();
        $nlog->createEntry();
        self::getTable()->database->commitChanges();
        return $nlog;
    }
    
    //Instance
    private $news_id;
    
    public function __construct($lt=null) {
        parent::__construct($lt);
        if($lt instanceof NewsLog) {
            $this->news_id = $lt->news_id;
        }
    }
    
    public function getNewsID() {return $this->news_id;}
    
    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        if($field->getName() == 'news_id') {
            return $this->news_id;
        }
        return null;
    }

    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'news_id') {
            $this->news_id = intval($value);
            return true;
        }
        return false;
    }
}
