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

/**
 * Description of User
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class Password extends \VirtualClass {
    public static $PASSWORD_MIN_LENGTH = 6;
    
    public static function isValidPassword($password) {
        if($password == null) return false;
        if(strlen($password) < Password::$PASSWORD_MIN_LENGTH) return false;
        if(!preg_match('/[A-Z]+/', $password)) return false;
        if(!preg_match('/[a-z]+/', $password)) return false;
        if(!preg_match('/[0-9]+/', $password)) return false;
        //if(!preg_match('/[\W]+/', $password)) return false;
        return true;
    }
    
    public static function getFields() {
        $list = parent::getFields();
        $list->add(User::getUsersAddon()->getPasswordsTable()->getFields());
        return $list;
    }
    
    public static function getByUserID($id) {
        return Password::getByFieldValue(User::getUsersAddon()->getPasswordsTable()->getField('user_id'), intval($id));
    }
    
    public static function generateForUser($user, $password) {
        if(!($user instanceof User)) throw new \Exception("Not a valid user.");
        $salt = generateSalt();
        $crypt = crypt($password, $salt);
        unset($password);
        
        $password = new Password();
        $password->password = $crypt;
        $password->salt = $salt;
        $password->time = new \DateTime();
        $password->user_id = $user->getID();
        
        return $password;
    }


    //Instance
    private $id;
    private $password;
    private $salt;
    private $time;
    private $user_id;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getID() {return $this->id;}
    public function getTime() {return $this->time;}
    public function getUserID() {return $this->user_id;}
    
    public function setID($id) {$this->id = $id;}
    public function setTime($time) {return $this->time = $time;}
    public function setUserID($user_id) {$this->user_id = $user_id;}

    /**
     *  Be careful with this, this function CAN return the password AND salt!
     */
    public function getValue($field) {
        if($field->getName() == 'id') return $this->id;
        if($field->getName() == 'password') return $this->password;
        if($field->getName() == 'salt') return $this->salt;
        if($field->getName() == 'time') return $this->time;
        if($field->getName() == 'user_id') return $this->user_id;
        return null;
    }

    /**
     * 
     * @param \VirtualField $field
     * @param mixed $value
     * @return bool If true then it matches something (used for inheritence)
     */
    public function setField($field, $value) {
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'password') {
            $this->password = $value;
            return true;
        } else if($field->getName() == 'salt') {
            $this->salt = $value;
            return true;
        } else if($field->getName() == 'time') {
            $this->time = $value;
            return true;
        } else if($field->getName() == 'user_id') {
            $this->user_id = $value;
            return true;
        }
        return false;
    }
    
    public function compare(&$pwd) {
        //Crypty
        $safe = crypt($pwd, $this->salt);
        return $safe === $this->password;
    }
    
    public function login(&$pwd) {
        $safe = crypt($pwd, $this->salt);
        $pwd = '';
        $pwd = null;
        unset($pwd);
        return $this->loginCrypted($safe);
    }
    
    public function loginCrypted($safe) {
        if(!($safe === $this->password)) throw new \Exception();
        
        //Store Password into session
        $_SESSION["user"] = $this->getUserID();
        $_SESSION["pass"] = $safe;//Do NOT store $this->password.
        User::$LOGGED_IN_USER = User::getByID($this->getUserID());
        return true;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id
        );
    }
}
