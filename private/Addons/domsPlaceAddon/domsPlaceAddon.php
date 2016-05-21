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

/**
 * Description of domsPlaceAddon
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class domsPlaceAddon extends \Addon {
    private static $INSTANCE;
    
    /**
     * 
     * @return domsPlaceAddon
     */
    public static function getInstance() {
        return static::$INSTANCE;
    }
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getNewsTable() {return static::getInstance()->database->getTable('News');}
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getNewsLogsTable() {return static::getInstance()->database->getTable('NewsLogs');}
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getNewsPostedLogsTable() {return static::getInstance()->database->getTable('NewsPostedLogs');}
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getEnquiryEmailsTable() {return static::getInstance()->database->getTable('EnquiryEmails');}
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getProjectsTable() {return static::getInstance()->database->getTable('Projects');}
    
    /**
     * 
     * @return \VirtualTable
     */
    public static function getProjectLogsTable() {return static::getInstance()->database->getTable('ProjectLogs');}
    
    /**
     * 
     * @return \UsersAddon\RegisteredUser
     */
    public static function getUserDominic() {
        return static::getInstance()->dominic;
    }
    
    public function formatHTML($html) {
        return strip_tags($html, '<b><i><img><a><u><em><span><h1><h2><h3><h4><h5><h6><h7><h8><h9><video><br><code>');
    }
    
    //Instance
    private $database_connection;
    private $database;
    
    private $dominic;//Me!
    
    public function __construct() {
        parent::__construct('domsPlaceAddon', '1.0.0');
        static::$INSTANCE = $this;
    }
    
    public function getDependancies() {
        $x = parent::getDependancies();
        $x->add('UsersAddon');
        $x->add('PermissionsAddon');
        $x->add('LogsAddon');
        return $x;
    }
    
    public function onEnable() {
        //Try connecting to the database.
        import('Database.ManagedConnection');
        
        try {
            $this->database_connection = \ManagedConnection::getMainConnection();
            $name = getconf('DATABASE_SCHEMA') === NULL ? "domsPlace" : getconf('DATABASE_SCHEMA');
            try {
                $this->database = \VirtualDatabase::getDatabase($name, $this->database_connection);
            } catch(\Exception $ex) {
                $this->database = new \VirtualDatabase($name);
                $this->database->setHandle($this->database_connection);
                $this->database->commitChanges();
            }
        } catch(\Exception $e) {
            throw new \Exception("Could not connect or create database.", 0, $e);
            //Failed to connect.
        }
        
        \UsersAddon\UsersAddon::getAddonByName('UsersAddon')->setDatabase($this->database);
        \PermissionsAddon\PermissionsAddon::getInstance()->setDatabase($this->database);
        \LogsAddon\LogsAddon::getInstance()->setDatabase($this->database);
        
        $this->import('Permissions.*');
        $this->import('Tables.*');
        
        try {
            Tables::setupTables($this->database);
        } catch(\Exception $e) {
            pageException($e);
        }
        
        $dom = \UsersAddon\RegisteredUser::getByName('Dominic');
        $this->dominic = $dom;
        
        //Load in the objects
        $this->import('Objects.*');
        $this->import('Logs.*');
        
        if(News::getCount() == 0) {//No News Posts
            $news = new News();
            $news->setTitle('Hello World');
            $news->setBody('This is a sample post.');
            $news->setTime(new \DateTime());
            $news->postNews($dom);
        }
        
        $this->import('Events.*');
        
        foreach($this->getFolder()->getChild("Events")->getDirectoryContents() as $listener) {
            if(!($listener instanceof \File)) continue;
            $x = $listener->getNameWithoutExtension();
            $x = "\domsPlaceAddon\\$x";
            $obj = new $x($this);
        }
        
        $this->import('Listeners.*');
        foreach($this->getFolder()->getChild("Listeners")->getDirectoryContents() as $listener) {
            if(!($listener instanceof \File)) continue;
            $x = $listener->getNameWithoutExtension();
            $x = "\domsPlaceAddon\\$x";
            $obj = new $x($this);
        }
    }
}