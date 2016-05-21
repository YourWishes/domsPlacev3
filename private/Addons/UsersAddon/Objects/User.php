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

import('Database.*');
import('Database.VirtualDatabase.*');
import('Database.VirtualDatabase.VirtualQuery.*');
import('Database.VirtualDatabase.VirtualClass.VirtualClass');

/**
 * Description of User
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class User extends \VirtualClass implements \JsonSerializable, \Comparable {
    public static $LOGGED_IN_USER;
    
    public static function getUsersAddon() {
        //Simple wrapper to get the users addon.
        $addon = UsersAddon::getAddonByName('UsersAddon');
        if(!($addon instanceof \UsersAddon\UsersAddon)) throw new \Exception();
        return $addon;
    }
    
    public static function getFields() {
        $list = parent::getFields();
        $list->add(User::getUsersAddon()->getUsersTable()->getFields());
        return $list;
    }
    
    public static function getByEntry($virtual_entry) {
        $x = parent::getByEntry($virtual_entry);
        if(!($x instanceof User) || $x instanceof RegisteredUser) return $x;
        $reg = $x->isRegistered();
        if(!($reg instanceof RegisteredUser)) return $x;
        return $reg;
    }
    
    /**
     * Try not to call too much.
     * @return \UsersAddon\RegisteredUser
     */
    public static function getLoggedInUser() {
        if(isset(User::$LOGGED_IN_USER)) return User::$LOGGED_IN_USER;
        if(!isset($_SESSION['user'])) return User::logout ();
        $id = $_SESSION['user'];
        
        $user = User::getByID($id);
        if($user === null || !($user instanceof User)) {
            return User::logout();
        }
        $user = $user->isRegistered();
        if($user === null || !($user instanceof RegisteredUser)) {
            return User::logout();
        }
        
        //User is "Registered"
        
        if(isset($_SESSION['pass'])) {
            $password = $user->getPassword();
            if(!($password instanceof Password)) {
                return User::logout();
            }
            try {
                $password->loginCrypted($password);
            } catch(\Exception $e) {
                if(!($password instanceof Password)) {
                    return User::logout();
                }
            }
            return User::$LOGGED_IN_USER = $user;
        }
        
        return User::logout();
    }
    
    public static function isLoggedIn() {
        return static::getLoggedInUser() instanceof RegisteredUser;
    }
    
    /**
     * Logout is not smart, but it is powerful, it will log you out and destroy
     * the session, whether you're logged in or not.
     * 
     * Always returns null.
     * @return null
     */
    public static function logout() {
        unset($_SESSION['user']);
        unset($_SESSION['pass']);
        return User::$LOGGED_IN_USER = null;
    }

    //Instance
    private $id;
    
    private $password;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getID() {return $this->id;}
    
    /**
     * 
     * @return Password|null
     */
    public function getPassword() {
        if(isset($this->password) && $this->password instanceof Password) return $this->password;
        return $this->password = Password::getByUserID($this->getID());
    }
    
    //used for inheritence
    public function setID($id) {
        $this->id = intval($id);
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
        }
        return false;
    }
    
    public function getIgnoredFields() {
        $x = parent::getIgnoredFields();
        $x->add(User::getUsersAddon()->getUsersTable()->getField('id'));
        return $x;
    }
    
    public function getValue($field) {
        if($field->getName() == "id") return $this->id;
        return null;
    }
    
    public function getGroups() {return \PermissionsAddon\Group::getUsersGroups($this);}
    
    /**
     * 
     * @return \File
     */
    public function getImage() {
        $rootfile = \File::getDocumentRoot()->getChild('user');
        if(!$rootfile->exists()) {
            $rootfile->mkdir();
        }
        $rootfile = $rootfile->getChild('images');
        if(!$rootfile->exists()) {
            $rootfile->mkdir();
        }
        $file = $rootfile->getChild('u_'.$this->getID().'.png');
        if($file->exists()) {
            return $file;
        }
        $file = $rootfile->getChild('no_profile.png');
        return $file;
    }
    
    /**
     * 
     * @return \UsersAddon\RegisteredUser
     */
    public function isRegistered() {
        if($this instanceof RegisteredUser) return $this;
        return RegisteredUser::getByID($this->getID());
    }

    public function compare(\Comparable $to) {
        if(!($to instanceof User)) return false;
        return $to->getID() == $this->getID();
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id
        );
    }
}
