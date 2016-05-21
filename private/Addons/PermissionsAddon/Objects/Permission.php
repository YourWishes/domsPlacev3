<?php

namespace PermissionsAddon;

if (!defined('MAIN_INCLUDED'))
    throw new \Exception();

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
class Permission extends \VirtualClass implements \JsonSerializable {
    public static function getWildcardPermission() {
        return static::registerPermission('All Permissions', 'Automatically grants full access to everything, do not use in a live environment.');
    }
    
    public static function getFields() {
        $x = parent::getFields();
        $x->add(static::getTable()->getFields());
        return $x;
    }
    
    public static function getByName($name) {
        return static::getByFieldValue(static::getTable()->getField('name'), $name);
    }
    
    /**
     * @return \VirtualTable
     */
    public static function getTable() {
        return PermissionsAddon::getInstance()->getPermissionsTable();
    }
    
    public static function noPermissionResponse(&$request) {
        if(!($request instanceof \AjaxRequest)) throw new \Exception("Not a valid AjaxRequest");
        $request->send('No Permission');
    }
    
    /**
     * 
     * @param int $id
     * @return \ArrayList List of Permissions
     */
    public static function getByGroupID($id) {
        //We have to build a freakin' query!
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $claws = new \WhereClause(PermissionsAddon::getInstance()->getGroupPermissionsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $id);
        $query->addClause($claws);
        $claws = new \WhereClause(static::getTable()->getField('id'), \ClauseOperator::$EQUALS, PermissionsAddon::getInstance()->getGroupPermissionsTable()->getField('permission_id'));
        $query->addClause($claws);
        
        $result = $query->fetch(static::getTable()->database);
        
        $perms = new \ArrayList('\PermissionsAddon\Permission');
        foreach($result as $r) {
            $perm = static::getByEntry($r);
            $perms->add($perm);
        }
        return $perms;
    }
    
    public static function getUsersPermissions(&$user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Not a valid user.");
        $table = PermissionsAddon::getInstance()->getUserPermissionsTable();
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $claws = new \WhereClause($table->getField('user_id'), \ClauseOperator::$EQUALS, $user->getID());
        $query->addClause($claws);
        $claws = new \WhereClause(static::getTable()->getField('id'), \ClauseOperator::$EQUALS, $table->getField('permission_id'));
        $query->addClause($claws);
        
        $result = $query->fetch(static::getTable()->database);
        
        $perms = new \ArrayList('\PermissionsAddon\Permission');
        foreach($result as $r) {
            $perm = static::getByEntry($r);
            $perms->add($perm);
        }
        return $perms;
    }
    
    public static function getAllUsersPermissions(&$user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Not a valid user.");
        $base_perms = Permission::getUsersPermissions($user);
        
        $groups = Group::getUsersGroups($user);
        foreach($groups as $group) {
            if(!($group instanceof Group)) continue;
            $base_perms->add($group->getAllPermissions());
        }
        return $base_perms;
    }
    
    public static function registerPermission($name, $desc=null) {
        $permission = static::getByName($name);
        if($permission === null) {
            $permission = new Permission();
            $permission->setName($name);
            if($desc !== null) $permission->setDescription ($desc);
            $permission->createEntry();
            static::getTable()->database->commitChanges();
            
            $permission = static::getByName($name);
        }
        return $permission;
    }
    
    //Instance
    private $id;
    private $name;
    private $description;

    public function __construct() {
        
    }

    
    public function getID() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDescription() {
        return $this->description;
    }
    

    public function setID($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
    

    public function getValue($field) {
        if(!($field instanceof \VirtualField)) return null;
        if($field->getName() == 'id') return $this->id;
        if($field->getName() == 'name') return $this->name;
        if($field->getName() == 'description') return $this->description;

        return null;
    }

    public function setField($field, $value) {
        if(!($field instanceof \VirtualField)) return false;
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'name') {
            $this->name = $value;
            return true;
        } else if($field->getName() == 'description') {
            $this->description = $value;
            return true;
        }

        return false;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description
        );
    }

    public function canUserPerform($user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Not a valid user.");
        
        //Get Perms
        $users_perms = static::getAllUsersPermissions($user);
        if($users_perms->contains(Permission::getWildcardPermission())) return true;//WILDCARD CHECKING
        return $users_perms->contains($this);
    }
    
    public function addToUser($user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception('Not a valid User.');
        //We will need to manually insert this.
        $table = PermissionsAddon::getInstance()->getUserPermissionsTable();
        
        $map = new \HashMap('VirtualField');
        $prmfld = $table->getField('permission_id');
        $usrfld = $table->getField('user_id');
        $map->putVal($prmfld, $this->getID());
        $map->putVal($usrfld, $user->getID());
        
        $ve = $table->createEntry($map);
        return $ve; 
    }
    
    public function removeFromUser($user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Excpetion("Invalid User");
        
        $clauses = new \ArrayList('\WhereClause');
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getUserPermissionsTable()->getField('user_id'), \ClauseOperator::$EQUALS, $user->getID()));
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getUserPermissionsTable()->getField('permission_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $query = new \DeleteQuery(PermissionsAddon::getInstance()->getUserPermissionsTable(), $clauses);
        $query->fetch(static::getTable()->database);
    }

    public function canCurrentUser() {
        //TODO: Add Default Group Support
        if(!\UsersAddon\User::isLoggedIn()) return false;
        $user = \UsersAddon\User::getLoggedInUser();
        return $this->canUserPerform($user);
    }
}
