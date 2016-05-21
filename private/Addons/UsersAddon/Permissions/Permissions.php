<?php
namespace UsersAddon;

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
    public static function VIEW_USERS() {return static::register('View Users', "View the list of users that exist.");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function ADD_USER() {return static::register('Add User', "Add a user to the system.");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function SET_PASSWORD() {return static::register('Set Password', "Force change the password of a user.");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function ADD_GROUP() {return static::register('Add Group to User', "Add a group to a user");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function REMOVE_GROUP() {return static::register('Remove Group from User', "Remove a group from a user");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function ADD_PERMISSION() {return static::register('Add Permission to User', "Add a permission to a user");}
    
    /**
     * 
     * @return \PermissionsAddon\Permission
     */
    public static function REMOVE_PERMISSION() {return static::register('Remove Permission from User', "Remove a permission from a user");}
}