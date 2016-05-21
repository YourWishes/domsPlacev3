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
class NewsPostedLog extends \domsPlaceAddon\NewsLog {
    public static function getFields() {
        $list = parent::getFields();
        $list->add(static::getTable()->getFields());
        return $list;
    }
    
    public static function getTable() {
        return domsPlaceAddon::getNewsPostedLogsTable();
    }
    
    public static function getWarrantyFormSubmittedLogType() {
        return \LogsAddon\LogType::register('News Posted');
    }
    
    public static function createForPost($user, $news) {
        $log = parent::createForPost($news, static::getWarrantyFormSubmittedLogType());
        return self::create($user, $log);
    }
    
    public static function create($user, $log) {
        if(!($log instanceof NewsLog)) throw new \Exception("Invalid Log");
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Invalid User");
        $nlog = new NewsPostedLog($log);
        $nlog->poster_id = $user->getID();
        $nlog->createEntry();
        self::getTable()->database->commitChanges();
        return $nlog;
    }
    
    public static function getPostedLogFromPost($news) {
        if(!($news instanceof News)) throw new \Exception("Invalid Post.");
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $query->addClause(new \WhereClause(self::getIDField(), \ClauseOperator::$EQUALS, NewsLog::getIDField()));
        $query->addClause(new \WhereClause(NewsLog::getIDField(), \ClauseOperator::$EQUALS, \LogsAddon\Log::getIDField()));
        $rs = $query->fetch(static::getTable()->database);
        if($rs->size() < 1) return null;
        return static::getByEntry($rs->get(0));
    }
    
    //Instance
    private $poster_id;
    
    public function __construct($lt=null) {
        parent::__construct($lt);
        if($lt instanceof NewsPostedLog) {
            $this->poster_id = $lt->poster_id;
        }
    }
    
    public function getPosterID() {return $this->poster_id;}
    
    /**
     * 
     * @return \UsersAddon\User
     */
    public function getPoster() {return \UsersAddon\User::getByID($this->poster_id);}
    
    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        if($field->getName() == 'poster_id') {
            return $this->poster_id;
        }
        return null;
    }

    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'poster_id') {
            $this->poster_id = intval($value);
            return true;
        }
        return false;
    }
}
