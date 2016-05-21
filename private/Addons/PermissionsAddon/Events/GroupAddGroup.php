<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupAddGroup extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupAddGroup::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('addGroupToGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupAddGroup::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::ADD_INHERITED_GROUP()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No Group ID.');
        if(!isset($data["inherited_id"]) || !is_numeric($data["inherited_id"])) $request->send ('No Inherited ID.');
        
        $groupid = intval($data["id"]);
        $inheritedid = intval($data["inherited_id"]);
        
        $group = Group::getByID($groupid);
        if(!($group instanceof Group)) $request->send('No Group');
        
        $inherited = Group::getByID($inheritedid);
        if(!($inherited instanceof Group)) $request->send('No Inherited Group');
        
        if($group == $inherited) $request->send('Cannot self inherit.');
        
        $ventry = $group->addInheritedGroup($inherited);
        $ventry->getTable()->database->commitChanges();
        
        $request->send($inherited);
    }
}