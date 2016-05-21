<?php
namespace PermissionsAddon;

if (!defined('MAIN_INCLUDED')) throw new \Exception();

class PermissionsAddon extends \Addon {
    private static $INSTANCE;
    
    /**
     * 
     * @return PermissionsAddon
     */
    public static function getInstance() {
        return PermissionsAddon::$INSTANCE;
    }
    
    //Instance
    private $database;

    public function __construct() {
        parent::__construct('PermissionsAddon', '1.00');
        PermissionsAddon::$INSTANCE = $this;
    }
    
    /**
     * @return \VirtualTable
     */ 
    public function getPermissionsTable() {
        if(!($this->database instanceof \VirtualDatabase)) {
            throw new \Exception("No Conn.");
        }
        return $this->database->getTable('Permissions');
    }
    /**
     * @return \VirtualTable
     */
    public function getGroupsTable() {return $this->database->getTable('Groups');}
    /**
     * @return \VirtualTable
     */
    public function getGroupPermissionsTable() {return $this->database->getTable('GroupPermissions');}
    /**
     * @return \VirtualTable
     */
    public function getGroupGroupsTable() {return $this->database->getTable('GroupGroups');}
    /**
     * @return \VirtualTable
     */
    public function getUserPermissionsTable() {return $this->database->getTable('UserPermissions');}
    /**
     * @return \VirtualTable
     */
    public function getUserGroupsTable() {return $this->database->getTable('UserGroups');}
    
    /**
     * @return \VirtualDatabase
     */
    public function getDatabase() {return $this->database;}

    public function setDatabase($db) {
        if (!($db instanceof \VirtualDatabase)) {
            throw new Exception('Invalid database.');
        }
        $this->database = $db;
        $this->setupTableDatabase();
    }

    private function setupTableDatabase() {
        //Create our tables.
        $permissions_table = $this->database->getTable('Permissions');
        if($permissions_table === null) {
            $permissions_table = new \VirtualTable('Permissions');
            $permissions_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "primary_key" => true,
                "auto_increment" => true,
                "nullable" => false
            )));
            $permissions_table->addField(new \VirtualField('name', \VirtualFieldType::$VARCHAR, array(
                "unique_key" => true,
                "nullable" => false,
                "max_length" => 32
            )));
            $permissions_table->addField(new \VirtualField('description', \VirtualFieldType::$VARCHAR, array(
                "max_length" => 256
            )));
            
            $this->database->addTable($permissions_table);
        }
        
        $groups_table = $this->database->getTable('Groups');
        if($groups_table === null) {
            $groups_table = new \VirtualTable('Groups');
            $groups_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "primary_key" => true,
                "auto_increment" => true,
                "nullable" => false
            )));
            $groups_table->addField(new \VirtualField('name', \VirtualFieldType::$VARCHAR, array(
                "unique_key" => true,
                "nullable" => false,
                "max_length" => 32
            )));
            $groups_table->addField(new \VirtualField('description', \VirtualFieldType::$VARCHAR, array(
                "max_length" => 256
            )));
            $groups_table->addField(new \VirtualField('isDefault', \VirtualFieldType::$BOOLEAN, array(
                "nullable" => false
            )));
            
            $this->database->addTable($groups_table);
        }
        
        $group_permissions_table = $this->database->getTable('GroupPermissions');
        if($group_permissions_table === null) {
            $group_permissions_table = new \VirtualTable('GroupPermissions');
            $group_permissions_table->addForeignField('group_id', $groups_table->getField('id'), array(
                "primary_key" => true
            ));
            $group_permissions_table->addForeignField('permission_id', $permissions_table->getField('id'), array(
                "primary_key" => true
            ));
            
            $this->database->addTable($group_permissions_table);
        }
        
        $user_permissions_table = $this->database->getTable('UserPermissions');
        if($user_permissions_table === null) {
            $user_permissions_table = new \VirtualTable('UserPermissions');
            $user_permissions_table->addForeignField('user_id', \UsersAddon\User::getUsersAddon()->getUsersTable()->getField('id'), array(
                "primary_key" => true
            ));
            $user_permissions_table->addForeignField('permission_id', $permissions_table->getField('id'), array(
                "primary_key" => true
            ));
            $this->database->addTable($user_permissions_table);
        }
        
        $user_groups_table = $this->database->getTable('UserGroups');
        if($user_groups_table === null) {
            $user_groups_table = new \VirtualTable('UserGroups');
            $user_groups_table->addForeignField('user_id', \UsersAddon\User::getUsersAddon()->getUsersTable()->getField('id'), array(
                "primary_key" => true
            ));
            $user_groups_table->addForeignField('group_id', $groups_table->getField('id'), array(
                "primary_key" => true
            ));
            
            $this->database->addTable($user_groups_table);
        }
        
        $group_inheritence_table = $this->database->getTable('GroupGroups');
        if($group_inheritence_table === null) {
            $group_inheritence_table = new \VirtualTable('GroupGroups');
            $group_inheritence_table->addForeignField('group_id', $groups_table->getField('id'), array(
                "primary_key" => true
            ));
            $group_inheritence_table->addForeignField('inherited_group_id', $groups_table->getField('id'), array(
                "primary_key" => true
            ));
            
            $this->database->addTable($group_inheritence_table);
        }
        
        $this->database->commitChanges();
        
        $size = \PermissionsAddon\Permission::getCount();//Load in from database to cache.
        $wildcard_permission = Permission::getWildcardPermission();
        if($size == 0) {    
            //$this->database->commitChanges();
        }
        
        $size = \PermissionsAddon\Group::getCount();
        if($size == 0) {
            $defaultGroup = new Group();
            $defaultGroup->setName('Default');
            $defaultGroup->setDescription('Default Group with no permissions.');
            $defaultGroup->setDefault(true);
            $defaultGroup->createEntry();
            
            $group = new Group();
            $group->setName('Superuser');
            $group->setDescription('Superuser Group with all permissions. Ideally try to not use this group.');
            
            $ventry = $group->createEntry();
            $this->database->commitChanges();
            $group->setID($ventry->getData()->getComp(Group::getIDField()));
            
            $group->addPermission($wildcard_permission);
            $this->database->commitChanges();
        }
    }

    public function getDependancies() {
        $x = parent::getDependancies();
        $x->add('UsersAddon');
        return $x;
    }

    public function onEnable() {
        $this->import('Objects.*');
        $this->import('Permissions.*');
        $this->import('Events.*');
        
        new GroupAdd();
        new GroupDelete();
        new GroupSave();
        new GroupGetPermissions();
        new GroupRemovePermission();
        new GroupAddPermission();
        new GroupAddGroup();
        new GroupGetGroups();
        new GroupRemoveGroup();
    }

}
