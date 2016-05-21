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
 * Description of Tables
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class Tables {
    public static function setupTables($db) {
        if(!($db instanceof \VirtualDatabase)) throw new \Exception("Not a valid Database.");
        
        $news_table = $db->getTable('News');
        if(!($news_table instanceof \VirtualTable)) {//AKA, doesn't exist
            $news_table = new \VirtualTable('News');
            $news_table->addField(static::getGenericID());
            $news_table->addField(new \VirtualField('title', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 32
            )));
            $news_table->addField(static::getGenericTimestamp());
            $news_table->addField(new \VirtualField('body', \VirtualFieldType::$TEXT, array(
                "nullable" => false
            )));
            $news_table->addField(new \VirtualField('hidden', \VirtualFieldType::$BOOLEAN, array(
                "nullable" => false,
                "default" => false
            )));
            
            $db->addTable($news_table);
        }
        
        $news_logs_table = $db->getTable('NewsLogs');
        if(!($news_logs_table instanceof \VirtualTable)) {
            $news_logs_table = new \VirtualTable('NewsLogs');
            $news_logs_table->addForeignField('id', \LogsAddon\Log::getIDField(), array(
                "primary_key" => true,
                "nullable" => false
            ));
            $news_logs_table->addForeignField('news_id', $news_table->getField('id'), array(
                "nullable" => false
            ));
            $db->addTable($news_logs_table);
        }
        
        $news_posted_logs_table = $db->getTable('NewsPostedLogs');
        if(!($news_posted_logs_table instanceof \VirtualTable)) {
            $news_posted_logs_table = new \VirtualTable('NewsPostedLogs');
            $news_posted_logs_table->addForeignField('id', $news_logs_table->getField('id'), array(
                "primary_key" => true,
                "nullable" => false
            ));
            $news_posted_logs_table->addForeignField('poster_id', \UsersAddon\User::getIDField(), array(
                "nullable" => false
            ));
            $db->addTable($news_posted_logs_table);
        }
        
        $banners_table = $db->getTable('PageBanner');
        if(!($banners_table instanceof \VirtualTable)) {//AKA, doesn't exist
            $banners_table = new \VirtualTable('PageBanner');
            $banners_table->addField(static::getGenericID());
            $banners_table->addField(new \VirtualField('title', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 32
            )));
            $banners_table->addField(new \VirtualField('link', \VirtualFieldType::$TEXT, array(
                "nullable" => false
            )));
            $banners_table->addField(new \VirtualField('active', \VirtualFieldType::$BOOLEAN, array(
                "nullable" => false,
                "default" => false
            )));
            
            
            $db->addTable($banners_table);
        }
        
        $enquiry_emails = $db->getTable('EnquiryEmails');
        if(!($enquiry_emails instanceof \VirtualTable)) {
            $enquiry_emails = new \VirtualTable('EnquiryEmails');
            $enquiry_emails->addField(static::getGenericID());
            $enquiry_emails->addField(static::getGenericEmail());
            $enquiry_emails->addField(new \VirtualField('message', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 8192
            )));
            $enquiry_emails->addField(new \VirtualField('name', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 128
            )));
            
            $db->addTable($enquiry_emails);
        }
        
        $projects_table = $db->getTable('Projects');
        if(!($projects_table instanceof \VirtualTable)) {
            $projects_table = new \VirtualTable('Projects');
            $projects_table->addField(static::getGenericID());
            $projects_table->addField(new \VirtualField('title', \VirtualFieldType::$VARCHAR, array(
                "nullable" => false,
                "max_length" => 64
            )));
            $projects_table->addField(new \VirtualField('description', \VirtualFieldType::$TEXT, array(
                "nullable" => false
            )));
            
            $db->addTable($projects_table);
        }
        
        $project_logs_table = $db->getTable('ProjectLogs');
        if(!($project_logs_table instanceof \VirtualTable)) {
            $project_logs_table = new \VirtualTable('ProjectLogs');
            $project_logs_table->addForeignField('id', \LogsAddon\LogUser::getIDField(), array(
                "primary_key" => true,
                "nullable" => false
            ));
            $project_logs_table->addForeignField('project_id', $projects_table->getField('id'), array(
                "nullable" => false
            ));
            $db->addTable($project_logs_table);
        }
        
        $db->commitChanges();
    }
    
    /**
     * Generates a fairly generic ID field for use on tables.
     * @return \VirtualField
     */
    public static function getGenericID() {
        $field = new \VirtualField('id', \VirtualFieldType::$INTEGER, array(
            "primary_key" => true,
            "auto_increment" => true,
            "nullable" => false
        ));
        return $field;
    }
    
    /**
     * Generates a fairly generic ID field for use on tables.
     * @return \VirtualField
     */
    public static function getGenericEmail() {
        $field = new \VirtualField('email', \VirtualFieldType::$VARCHAR, array(
            "nullable" => false,
            "max_length" => 254
        ));
        return $field;
    }
    
    /**
     * Generates a fairly generic Timestamp-like field for use on tables.
     * @return \VirtualField
     */
    public static function getGenericTimestamp() {
        $field = new \VirtualField('time', \VirtualFieldType::$DATETIME, array(
            "nullable" => false
        ));
        return $field;
    }
}
