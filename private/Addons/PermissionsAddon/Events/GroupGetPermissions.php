<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupGetPermissions extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupGetPermissions::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('getGroupsPermissions');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupGetPermissions::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::VIEW_GROUPS_PERMISSIONS()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No ID.');
        
        $id = intval($data["id"]);
        $group = Group::getByID($id);
        if(!($group instanceof Group)) $request->send('No Group');
        
        $perms = $group->getPermissions();
        $request->send($perms);
    }
}