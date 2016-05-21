<?php
namespace domsPlaceAddon;

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
 * Description of News
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class Project extends \VirtualClass implements \JsonSerializable {
    public static function getRecent($count=10) {
        
    }
    
    public static function getFields() {
        $x = parent::getFields();
        $x->add(static::getTable()->getFields());
        return $x;
    }
    
    /**
     * @return \VirtualTable
     */
    public static function getTable() {
        return domsPlaceAddon::getProjectsTable();
    }
    
    //Instance
    private $id;
    private $title;
    private $description;

    public function __construct() {
        parent::__construct();
    }
    
    public function getID() {return $this->id;}
    public function getTitle() {return $this->title;}
    public function getDescription() {return $this->description;}
    
    public function setID($id) {$this->id = $id;}
    public function setTitle($title) {$this->title = $title;}
    public function setDescription($description) {$this->description = $description;}
    
    /**
     * 
     * @return \File
     */
    private function getAssetsDirectory() {
        $top_dir = domsPlaceAddon::getInstance()->getFolder();
        $files_dir = $top_dir->getChild('Files');
        if(!$files_dir->exists()) $files_dir->mkdir();
        $projects_dir = $files_dir->getChild('Projects');
        if(!($projects_dir->exists())) $projects_dir->mkdir();
        
        $my_assets_dir = $projects_dir->getChild($this->getID());
        return $my_assets_dir;
    }
    
    /**
     * 
     * @return \File
     */
    private function getDefaultAssetsDirectory() {
        $top_dir = domsPlaceAddon::getInstance()->getFolder();
        $files_dir = $top_dir->getChild('Files');
        if(!$files_dir->exists()) $files_dir->mkdir();
        $projects_dir = $files_dir->getChild('Projects');
        if(!($projects_dir->exists())) $projects_dir->mkdir();
        
        $default_assets = $projects_dir->getChild('Default');
        return $default_assets;
    }
    
    /**
     * 
     * @return \File
     */
    public function getBanner() {
        $dir = $this->getAssetsDirectory();
        if(!$dir->exists() || !$dir->getChild('banner.png')->exists()) {
            $dir = $this->getDefaultAssetsDirectory();
        }
        $file = $dir->getChild('banner.png');
        return $file;
    }

    public function getValue($field) {
        if(!($field instanceof \VirtualField)) return null;
        if($field->getName() == 'id') return $this->id;
        if($field->getName() == 'title') return $this->title;
        if($field->getName() == 'description') return $this->description;

        return null;
    }

    public function setField($field, $value) {
        if(!($field instanceof \VirtualField)) return false;
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'title') {
            $this->title = $value;
            return true;
        } else if($field->getName() == 'description') {
            $this->description = $value;
            return true;
        }

        return false;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->getID(),
            "title" => $this->getTitle(),
            "description" => $this->getDescription()
        );
    }
}
