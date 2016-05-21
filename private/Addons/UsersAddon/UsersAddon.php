<?php
namespace UsersAddon;

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

class UsersAddon extends \Addon {
    public static $INSTANCE;
    
    /**
     * 
     * @return UsersAddon
     */
    public static function getInstance() {return UsersAddon::$INSTANCE;}
    
    //Instance
    private $database;

    public function __construct() {
        parent::__construct('UsersAddon', '1.00');
        UsersAddon::$INSTANCE = $this;
    }

    public function setDatabase($db) {
        if (!($db instanceof \VirtualDatabase))
            throw new Exception('Invalid database.');
        $this->database = $db;
        $this->setupTableDatabase();
    }

    /**
     * 
     * @return \VirtualTable
     */
    public function getUsersTable() {
        return $this->database->getTable('Users');
    }

    /**
     * 
     * @return \VirtualTable
     */
    public function getRegisteredUsersTable() {
        return $this->database->getTable('RegisteredUsers');
    }

    /**
     * 
     * @return \VirtualTable
     */
    public function getPasswordsTable() {
        return $this->database->getTable('Passwords');
    }

    private function setupTableDatabase() {
        if ($this->database->getTable('Users') === null) {
            $users_table = new \VirtualTable('Users');
            $users_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "primary_key" => true,
                "auto_increment" => true,
                "nullable" => false
            )));
            $this->database->addTable($users_table);
        } else {
            $users_table = $this->database->getTable('Users');
        }

        if ($this->database->getTable('Passwords') === null) {
            $passwords_table = new \VirtualTable('Passwords');
            $passwords_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "primary_key" => true,
                "auto_increment" => true,
                "nullable" => false
            )));
            $passwords_table->addField(new \VirtualField('password', \VirtualFieldType::$TEXT, array(
                "nullable" => false
            )));
            $passwords_table->addField(new \VirtualField('salt', \VirtualFieldType::$VARCHAR, array(
                "nullable" => true,
                "max_length" => 64
            )));
            $passwords_table->addField(new \VirtualField('time', \VirtualFieldType::$DATETIME, array(
                "nullable" => false
            )));
            $passwords_table->addForeignField('user_id', $this->getUsersTable()->getField('id'), array(
                "unique_key" => true
            ));

            $this->database->addTable($passwords_table);
        } else {
            $passwords_table = $this->database->getTable('Passwords');
        }

        if ($this->database->getTable('RegisteredUsers') === null) {
            $registered_users_table = new \VirtualTable('RegisteredUsers');
            $registered_users_table->addForeignField('id', $this->getUsersTable()->getField('id'), array(
                "primary_key" => true
            ));
            $registered_users_table->addField(new \VirtualField('name', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 128
            )));
            $registered_users_table->addField(new \VirtualField('email', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 128
            )));

            $this->database->addTable($registered_users_table);
        }

        $this->database->commitChanges();
    }

    public function onEnable() {
        $this->import('Objects.User');
        $this->import('Objects.RegisteredUser');
        $this->import('Objects.Password');

        $this->import('Forms.*');
        $this->import('Events.*');

        new UserLogout();
        new UserSubmitLogin();
        new UserSubmitRegistration();
        new UserAdd();
        new UserRemove();
        new UserSetPassword();
        
        if($this->isPermissionsAvailable()) {
            $this->import('Permissions.Permissions');
            
            new UserAddGroupToUser();
            new UserRemoveGroupFromUser();
            new UserAddPermissionToUser();
            new UserRemovePermissionFromUser();
        }
    }
    
    public function isPermissionsAvailable() {
        return \Addon::getAddonByName('PermissionsAddon') !== null;
    }

    public function getDependancies() {
        $x = parent::getDependancies();
        $x->add('FormsAddon');
        return $x;
    }
}
