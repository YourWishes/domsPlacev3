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
 * Description of Permissions
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class Permissions  {
    /**
     * 
     * @param type $name
     * @param type $desc
     * @return \PermissionsAddon\Permission
     */
    private static function register($name,$desc=null) {
        return \PermissionsAddon\Permission::registerPermission($name,$desc);
    }
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function CREATE_NEWS_POST() {return static::register('Create News Post', "Create a news post to appear on the homepage.");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function DELETE_NEWS_POST() {return static::register('Delete News Post', "Delete a news post. (Does not actually remove from the backend Database)");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function DELETE_OWN_NEWS_POST() {return static::register('Delete Own News Post', "Allows a user to delete their own news post without needing override permissions.");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function EDIT_NEWS_POST() {return static::register('Edit News Post', "Edit a news post.");}
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function EDIT_OWN_NEWS_POST() {return static::register('Edit Own News Post', "Allows a user to edit their own news post without needing override permissions.");}
}