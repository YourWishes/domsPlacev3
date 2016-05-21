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
 * Description of IPLog
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class IPLog extends Log {
    public static function getFields() {
        $list = parent::getFields();
        $list->add(self::getTable()->getFields());
        return $list;
    }
    
    public static function getTable() {
        return LogsAddon::getInstance()->getIPLogsTable();
    }
    
    public static function createForIP($log, $ip, $xforwarded=null) {
        if(strlen($ip) > 45) throw new \Exception("IP too long.");
        if(strlen($xforwarded > 45)) $xforwarded = null;
        
        //Validate IP
        if(!filter_var($ip, FILTER_VALIDATE_IP) && !filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_IPV6)) throw new \Exception("Invalid IP.");
        if(!filter_var($xforwarded, FILTER_VALIDATE_IP) && !filter_var($xforwarded,FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) $xforwarded = null;
        
        //Crate log
        $ulog = new IPLog($log);
        $ulog->ip = $ip;
        if($xforwarded !== null) $ulog->xforwarded = $xforwarded;
        $ulog->createEntry();
        static::getTable()->database->commitChanges();
        
        return $ulog;
    }
    
    public static function createForCurrent($log) {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $xforward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
        static::createForIP($log, $ip, $xforward);
    }
    
    //Instance
    private $ip;
    private $xforwarded;
    
    public function __construct($log=null) {
        parent::__construct($log);
        if($log instanceof IPLog) {
            $this->ip = $log->ip;
            $this->xforwarded = $log->xforwarded;
        }
    }
    
    public function getXForwarded() {return $this->xforwarded;}
    public function getIP() {return $this->ip;}

    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        
        if($field->getName() == 'ip') {
            return $this->ip;
        }else if($field->getName() == 'x_forwarded_for') {
            return $this->xforwarded;
        }
        return null;
    }

    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'ip') {
            $this->ip = $value;
            return true;
        } else if($field->getName() == 'x_forwarded_for') {
            $this->xforwarded = $value;
            return true;
        }
        return false;
    }
}
