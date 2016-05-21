<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupRemoveGroup extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupRemoveGroup::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('removeGroupFromGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupRemoveGroup::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::REMOVE_GROUPS_INHERITENCE()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No Group ID.');
        if(!isset($data["inheritence_id"]) || !is_numeric($data["inheritence_id"])) $request->send ('No Inherited Group ID.');
        
        $groupid = intval($data["id"]);
        $inheritedid = intval($data["inheritence_id"]);
        
        $group = Group::getByID($groupid);
        if(!($group instanceof Group)) $request->send('No Group');
        
        $inherited = Group::getByID($inheritedid);
        if(!($inherited instanceof Group)) $request->send('No Group');
        
        $group->removeInheritedGroup($inherited);
        PermissionsAddon::getInstance()->getDatabase()->commitChanges();
        
        $request->send(true);
    }
}