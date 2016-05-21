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
class Group extends \VirtualClass implements \JsonSerializable {
    private static $GROUP_CACHE;
    
    public static function getFields() {
        $x = parent::getFields();
        $x->add(static::getTable()->getFields());
        return $x;
    }
    
    /**
     * 
     * @param string $name
     * @return Group
     */
    public static function getByName($name) {
        return static::getByFieldValue(static::getTable()->getField('name'), $name);
    }
    
    /**
     * @return \VirtualTable
     */
    public static function getTable() {
        return PermissionsAddon::getInstance()->getGroupsTable();
    }
    
    public static function getUsersGroups(&$user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Not a valid user.");
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $claws = new \WhereClause(PermissionsAddon::getInstance()->getUserGroupsTable()->getField('user_id'), \ClauseOperator::$EQUALS, $user->getID());
        $query->addClause($claws);
        $claws = new \WhereClause(static::getTable()->getField('id'), \ClauseOperator::$EQUALS, PermissionsAddon::getInstance()->getUserGroupsTable()->getField('group_id'));
        $query->addClause($claws);
        
        $result = $query->fetch(static::getTable()->database);
        
        $groups = new \ArrayList('\PermissionsAddon\Group');
        foreach($result as $r) {
            $group = static::getByEntry($r);
            $groups->add($group);
        }
        return $groups;
    }
    
    //Instance
    private $id;
    private $name;
    private $description;
    private $default = false;
    
    private $permissions_cache;
    private $all_permissions_cache;

    public function __construct() {
        parent::__construct();
        if(!isset(Group::$GROUP_CACHE)) Group::$GROUP_CACHE = array();
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
    
    public function isDefault() {
        return $this->default;
    }
    
    public function setID($id) {
        $id = intval($id);
        if(isset($this->id)) unset(Group::$GROUP_CACHE[$this->id]);
        Group::$GROUP_CACHE[$id] = $this;
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setDefault($d) {
        if(!is_bool($d)) throw new \Exception("Not a boolean.");
        $this->default = $d;
    }
    
    public function addPermission($permission) {
        if(!($permission instanceof Permission)) throw new \Exception('Not a valid Permission.');
        //We will need to manually insert this.
        $table = PermissionsAddon::getInstance()->getGroupPermissionsTable();
        
        $map = new \HashMap('VirtualField');
        $gidfld = $table->getField('group_id');
        $prmfld = $table->getField('permission_id');
        $map->putVal($gidfld, $this->getID());
        $map->putVal($prmfld, $permission->getID());
        
        $ve = $table->createEntry($map);
        if($this->permissions_cache instanceof \ArrayList) $this->permissions_cache->add($permission);
        if($this->all_permissions_cache instanceof \ArrayList) $this->all_permissions_cache->add($permission);
        return $ve; 
    }
    
    public function addUser($user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception('Not a valid User.');
        //We will need to manually insert this.
        $table = PermissionsAddon::getInstance()->getUserGroupsTable();
        
        $map = new \HashMap('VirtualField');
        $gidfld = $table->getField('group_id');
        $usrfld = $table->getField('user_id');
        $map->putVal($gidfld, $this->getID());
        $map->putVal($usrfld, $user->getID());
        
        $ve = $table->createEntry($map);
        return $ve; 
    }
    
    /**
     * 
     * @param \PermissionsAddon\Group $group
     * @return \VirtualEntry
     * @throws \Exception
     */
    public function addInheritedGroup($group) {
        if(!($group instanceof Group)) throw new \Exception("Invalid Group");
        if($group->getID() == $this->getID()) throw new \Exception("Cannot self inherit.");
        $table = PermissionsAddon::getInstance()->getGroupGroupsTable();
        
        $map = new \HashMap('VirtualField');
        $gidfld = $table->getField('group_id');
        $prmfld = $table->getField('inherited_group_id');
        $map->putVal($gidfld, $this->getID());
        $map->putVal($prmfld, $group->getID());
        
        $ve = $table->createEntry($map);
        if($this->all_permissions_cache instanceof \ArrayList) unset($this->all_permissions_cache);
        return $ve; 
    }
    
    public function removePermissionByID($permission_id) {
        $clauses = new \ArrayList('\WhereClause');
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getGroupPermissionsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getGroupPermissionsTable()->getField('permission_id'), \ClauseOperator::$EQUALS, $permission_id));
        $query = new \DeleteQuery(PermissionsAddon::getInstance()->getGroupPermissionsTable(), $clauses);
        $query->fetch(static::getTable()->database);
        if($this->permissions_cache instanceof \ArrayList) unset($this->permissions_cache);
    }
    
    public function getPermissions() {
        if(isset($this->permissions_cache)) return $this->permissions_cache->createCopy();
        
        $this->permissions_cache = Permission::getByGroupID($this->getID());
        return $this->permissions_cache->createCopy();
    }
    
    public function getAllPermissions() {
        if(isset($this->all_permissions_cache)) return $this->all_permissions_cache;
        $my_perms = $this->getPermissions();
        
        //Now get the permissions from all the groups we are associated with
        $query = new \GetQuery();
        $query->addField(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('inherited_group_id'));
        $claws = new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID());
        $query->addClause($claws);
        
        
        $result = $query->fetch(static::getTable()->database);
        $id_field = PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('inherited_group_id');
        
        //Returns a list of group_ids, we can now use these.
        foreach($result as $r) {
            $data = $r->getData();
            
            $id = $data->get($id_field);
            
            $grp = Group::getByID($id);
            $prms = $grp->getAllPermissions();
            $my_perms->add($prms);
        }
        
        $this->all_permissions_cache = $my_perms;
        
        return $this->all_permissions_cache;
    }
    
    /**
     * 
     * @return \ArrayList
     */
    public function getInheritedGroups() {
        $query = new \GetQuery();
        $inhrt_fld = PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('inherited_group_id');
        $query->addField($inhrt_fld);
        $claws = new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS,$this->getID());
        $query->addClause($claws);
        $result = $query->fetch(static::getTable()->database);
        
        $groups = new \ArrayList('\PermissionsAddon\Group');
        foreach($result as $r) {
            $group = static::getByID($r->getData()->get($inhrt_fld));
            if(!($group instanceof Group)) continue;
            $groups->add($group);
        }
        return $groups;
    }
    
    public function removeInheritedGroup($group) {
        if(!($group instanceof Group)) throw new \Exception("Not a valid group.");
        $clauses = new \ArrayList('\WhereClause');
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('inherited_group_id'), \ClauseOperator::$EQUALS, $group->getID()));
        $query = new \DeleteQuery(PermissionsAddon::getInstance()->getGroupGroupsTable(), $clauses);
        $query->fetch(static::getTable()->database);
    }
    
    public function removeUser($user) {
        if(!($user instanceof \UsersAddon\User)) throw new \Exception("Not a valid group.");
        $clauses = new \ArrayList('\WhereClause');
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getUserGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $clauses->add(new \WhereClause(PermissionsAddon::getInstance()->getUserGroupsTable()->getField('user_id'), \ClauseOperator::$EQUALS, $user->getID()));
        $query = new \DeleteQuery(PermissionsAddon::getInstance()->getUserGroupsTable(), $clauses);
        $query->fetch(static::getTable()->database);
    }

    public function getValue($field) {
        if(!($field instanceof \VirtualField)) return null;
        if($field->getName() == 'id') return $this->id;
        if($field->getName() == 'name') return $this->name;
        if($field->getName() == 'description') return $this->description;
        if($field->getName() == 'isDefault') return $this->default;

        return null;
    }

    public function setField($field, $value) {
        if(!($field instanceof \VirtualField)) return false;
        if($field->getName() == 'id') {
            $this->setID($value);
            return true;
        } else if($field->getName() == 'name') {
            $this->name = $value;
            return true;
        } else if($field->getName() == 'description') {
            $this->description = $value;
            return true;
        } else if($field->getName() == 'isDefault') {
            $this->default = $value == 0 ? false : true;
            return true;
        }

        return false;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "default" => $this->default
        );
    }

    public function delete() {
        $q = new \DeleteQuery();
        $q->addClause(new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $q->fetch(static::getTable()->database);
        $q = new \DeleteQuery();
        $q->addClause(new \WhereClause(PermissionsAddon::getInstance()->getGroupGroupsTable()->getField('inherited_group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $q->fetch(static::getTable()->database);
        $q = new \DeleteQuery();
        $q->addClause(new \WhereClause(PermissionsAddon::getInstance()->getUserGroupsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $q->fetch(static::getTable()->database);
        $q = new \DeleteQuery();
        $q->addClause(new \WhereClause(PermissionsAddon::getInstance()->getGroupPermissionsTable()->getField('group_id'), \ClauseOperator::$EQUALS, $this->getID()));
        $q->fetch(static::getTable()->database);
        parent::delete();
    }
}
