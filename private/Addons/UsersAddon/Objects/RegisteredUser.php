<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new \Exception();

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

import('Email.Email');

/**
 * Description of User
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class RegisteredUser extends User {
    public static $MAX_USERNAME_LENGTH = 24;
    public static $USERNAME_REGEX = '/^[a-z0-9_-]{3,24}$/i';
    
    public static function isValidUsername($username) {
        if($username == null) return false;
        return preg_match(RegisteredUser::$USERNAME_REGEX, $username) &&
            strlen($username) <= User::getUsersAddon()->getRegisteredUsersTable()->getField('name')->max_length;
    }
    
    public static function getFields() {
        $list = parent::getFields();
        $list->add(User::getUsersAddon()->getRegisteredUsersTable()->getFields());
        return $list;
    }
    
    public static function getTable() {
        return User::getUsersAddon()->getRegisteredUsersTable();
    }
    
    public static function getByName($name) {
        return RegisteredUser::getByFieldValue(User::getUsersAddon()->getRegisteredUsersTable()->getField('name'), $name);
    }
    
    public static function getByEmail($email) {
        return RegisteredUser::getByFieldValue(User::getUsersAddon()->getRegisteredUsersTable()->getField('email'), $email);
    }
    
    public static function getIDField() {
        return static::getTable()->getField('id');
    }
    
    public static function getForUser($user) {
        if(!($user instanceof User)) throw new \Exception("Invalid User.");
        return RegisteredUser::getByID($user->getID());
    }

    /**
     * 
     * @param string $username
     * @param string $email
     * @return RegisteredUser
     * @throws \Exception
     */
    public static function registerNewUser($username, $email) {
        $existing = static::getByName($username);
        if($existing instanceof static) throw new \Exception('User already exists.');
        
        $user = new User();
        $entry = $user->createEntry();
        $entry->getTable()->database->commitChanges();
        
        $reg = new static();
        $reg->setID($entry->getData()->getComp(User::getIDField()));
        $reg->setUsername($username);
        $reg->setEmail($email);
        $entry = $reg->createEntry();
        $entry->getTable()->database->commitChanges();
        
        $fld = static::getIDField();
        $id = $entry->getData()->get($fld);
        return static::getByID($id);
    }
    
    //Instance
    private $username;
    private $email;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getUsername() {return $this->username;}
    public function getEmail() {return $this->email;}
    
    public function getLastLoginLog() {
        $query = new \GetQuery();
        $query->addFields(\LogsAddon\Log::getFields());
        $query->addFields(\LogsAddon\LogUser::getFields());
        $query->addClause(new \WhereClause(\LogsAddon\Log::getIDField(), \ClauseOperator::$EQUALS, \LogsAddon\LogUser::getIDField()));
        $query->addClause(new \WhereClause(\LogsAddon\LogUser::getTable()->getField('user_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $query->addClause(new \LimitClause(1));
        $query->addClause(new \OrderClause(\LogsAddon\Log::getTimeField(), false));
        $results = $query->fetch(self::getTable()->database);
        if($results->size() < 1) return;
        return \LogsAddon\Log::getByEntry($results[0]);
    }
    
    public function setUsername($username) {
        if(!(RegisteredUser::isValidUsername($username))) throw new \Exception("Invalid Username");
        $this->username = $username;
    }
    
    public function setEmail($email) {
        if(!\Email::isValidEmail($email)) throw new \Exception("Invalid Email");
        $this->email = $email;
    }
    
    /**
     * 
     * @param \VirtualField $field
     * @param mixed $value
     * @return bool If true then it matches something (used for inheritence)
     */
    public function setField($field, $value) {
        if(parent::setField($field, $value)) return true;
        if($field->getName() == 'name') {
            $this->username = $value;
            return true;
        } else if($field->getName() == 'email') {
            $this->email = $value;
            return true;
        }
        return false;
    }
    
    public function getValue($field) {
        $x = parent::getValue($field);
        if($x !== null) return $x;
        if($field->getName() == 'name') return $this->username;
        if($field->getName() == 'email') return $this->email;
        
        return null;
    }
    
    public function jsonSerialize() {
        $x = parent::jsonSerialize();
        $x["id"] = $this->getID();
        $x["username"] = $this->username;
        
        return $x;
    }
}
