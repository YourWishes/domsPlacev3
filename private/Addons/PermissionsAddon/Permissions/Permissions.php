<?php
namespace PermissionsAddon;

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
     * @return Permission
     */
    public static function VIEW_PERMISSIONS() {return static::register('View Permissions', "View the Systems Permissions.");}
    
    /**
     * 
     * @return Permission
     */
    public static function ADD_GROUP() {return static::register('Add Group', "Add a new group to the system.");}
    
    /**
     * 
     * @return Permission
     */
    public static function DELETE_GROUP() {return static::register('Delete Group', "Delete a group from the system.");}
    
    /**
     * 
     * @return Permission
     */
    public static function EDIT_GROUP() {return static::register('Edit Group', "Edit details of an existing group.");}
    
    /**
     * 
     * @return Permission
     */
    public static function VIEW_GROUPS_PERMISSIONS() {return static::register('View Groups Permissions', "View the permissions assigned to a group.");}
    
    /**
     * 
     * @return Permission
     */
    public static function REMOVE_GROUPS_PERMISSIONS() {return static::register('Remove Groups Permissions', "Remove permissions from a group.");}
    
    /**
     * 
     * @return Permission
     */
    public static function ADD_GROUPS_PERMISSIONS() {return static::register('Add Groups Permissions', "Add permissions to a group.");}
    
    /**
     * 
     * @return Permission
     */
    public static function ADD_INHERITED_GROUP() {return static::register('Add Inherited Group', "Add group to inherited groups.");}
    
    /**
     * 
     * @return Permission
     */
    public static function VIEW_GROUPS_INHERITENCE() {return static::register('View Inherited Groups', "View the groups a group inherits.");}
    
    /**
     * 
     * @return Permission
     */
    public static function REMOVE_GROUPS_INHERITENCE() {return static::register('Remove Inherited Groups', "Remove a group from a groups inherited list.");}
    
    /**
     * 
     * @return Permission
     */
    public static function VIEW_ADMIN_PANEL() {return static::register('View Admin Panel', "View the administration panel and site statistics.");}
}