<?php
namespace LogsAddon;
if (!defined('MAIN_INCLUDED')) throw new \Exception();

class LogsAddon extends \Addon {
    private static $INSTANCE;
    /**
     * 
     * @return LogsAddon
     */
    public static function getInstance() {
        return LogsAddon::$INSTANCE;
    }
    
    private $db;
    private $logs_table;
    private $logs_type_table;
    private $user_logs_table;
    private $page_logs_table;
    private $ip_logs_table;
    
    public function __construct() {
        parent::__construct('LogsAddon', '1.00');
        LogsAddon::$INSTANCE = $this;
    }
    
    /**
     * 
     * @return \VirtualTable
     */
    public function getLogsTable() {return $this->logs_table;}
    
    /**
     * 
     * @return \VirtualTable
     */
    public function getLogTypesTable() {return $this->logs_type_table;}
    
    /**
     * 
     * @return \VirtualTable
     */
    public function getLogUsersTable() {return $this->user_logs_table;}
    
    /**
     * 
     * @return \VirtualTable
     */
    public function getIPLogsTable() {return $this->ip_logs_table;}
    
    /**
     * 
     * @return \VirtualDatabase
     */
    public function getDatabase() {return $this->db;}
    
    /**
     * 
     * @return \VirtualTable
     */
    public function getPageLogsTable() {return $this->page_logs_table;}
    
    public function setDatabase($db, $commit=true) {
        if(!($db instanceof \VirtualDatabase)) throw new \Exception();
        $this->db = $db;
        
        $this->logs_type_table = $db->getTable('LogTypes');
        if($this->logs_type_table == null) {
            //LogTypes
            $this->logs_type_table = new \VirtualTable('LogTypes');
            $this->logs_type_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "nullable" => false,
                "primary_key" => true,
                "auto_increment" => true    
            )));
            $this->logs_type_table->addField(new \VirtualField('name', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "unique_key" => true,
                "max_length" => 256
            )));
            $db->addTable($this->logs_type_table);
        }
        
        $this->logs_table = $db->getTable('Logs');
        if($this->logs_table == null) {
            //Create our table
            $this->logs_table = new \VirtualTable('Logs');
            
            $this->logs_table->addField(new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
                "nullable" => false,
                "primary_key" => true,
                "auto_increment" => true
            )));
            $this->logs_table->addField(new \VirtualField('date', \VirtualFieldType::$DATETIME, array(
                "nullable" => false
            )));
            $this->logs_table->addForeignField('log_type_id', $this->logs_type_table->getField('id'));
            $db->addTable($this->logs_table);
        }
        
        if(\Addon::getAddonByName('UsersAddon') !== null) {
            //Users Addon found
            $this->user_logs_table = \UsersAddon\UsersAddon::getInstance();
            $this->user_logs_table = $db->getTable('UserLogs');
            if($this->user_logs_table == null) {
                //Create our table
                $this->user_logs_table = new \VirtualTable('UserLogs');
                $this->user_logs_table->addForeignField('id', $this->logs_table->getField('id'), array(
                    "primary_key" => true
                ));
                $this->user_logs_table->addForeignField('user_id', \UsersAddon\User::getIDField(), array(
                    "primary_key" => true
                ));
                $db->addTable($this->user_logs_table);
            }
        }
        
        $this->page_logs_table = $db->getTable('PageLogs');
        if($this->page_logs_table == null) {
            //Create our table
            $this->page_logs_table = new \VirtualTable('PageLogs');
            $this->page_logs_table->addForeignField('id', $this->logs_table->getField('id'), array(
                "primary_key" => true
            ));
            $this->page_logs_table->addField(new \VirtualField('url', \VirtualFieldType::$TEXT, array(
                "nullable" => false
            )));
            $db->addTable($this->page_logs_table);
        }
        
        $this->ip_logs_table = $db->getTable('IPLogs');
        if($this->ip_logs_table == null) {
            $this->ip_logs_table = new \VirtualTable('IPLogs');
            $this->ip_logs_table->addForeignField('id', $this->logs_table->getField('id'), array(
                "primary_key" => true
            ));
            $this->ip_logs_table->addField(new \VirtualField('ip', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 45
            )));
            $this->ip_logs_table->addField(new \VirtualField('x_forwarded_for', \VirtualFieldType::$VARCHAR, array(
                "nullable" => true,
                "max_length" => 45
            )));
            $db->addTable($this->ip_logs_table);
        }
        
        if($commit) $db->commitChanges();
    }

    public function onEnable() {
        $this->import('Objects.Log');
        $this->import('Objects.*');
        $this->import('Listeners.*');
        
        $pageListeners = new PageListeners();
    }
    
    public function getDependancies() {
        $x = parent::getDependancies();
        $x->add('UsersAddon');
        $x->add('PermissionsAddon');
        return $x;
    }
}