<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupRemovePermission extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupRemovePermission::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('removePermissionFromGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupRemovePermission::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::REMOVE_GROUPS_PERMISSIONS()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["group_id"]) || !is_numeric($data["group_id"])) $request->send ('No Group ID.');
        if(!isset($data["permission_id"]) || !is_numeric($data["permission_id"])) $request->send ('No Permission ID.');
        
        $groupid = intval($data["group_id"]);
        $permissionid = intval($data["permission_id"]);
        
        $group = Group::getByID($groupid);
        if(!($group instanceof Group)) $request->send('No Group');
        
        //$permission = Permission::getByID($permissionid);
        //if(!($permission instanceof Permission)) $request->send('No Permission');
        $group->removePermissionByID($permissionid);
        Group::getTable()->database->commitChanges();
        
        $request->send(true);
    }
}