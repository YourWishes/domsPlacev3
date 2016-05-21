<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupGetGroups extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupGetGroups::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('getGroupsGroups');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupGetGroups::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::VIEW_GROUPS_INHERITENCE()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No ID.');
        
        $id = intval($data["id"]);
        $group = Group::getByID($id);
        if(!($group instanceof Group)) $request->send('No Group');
        
        $request->send($group->getInheritedGroups());
    }
}