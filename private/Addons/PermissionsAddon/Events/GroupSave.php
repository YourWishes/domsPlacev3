<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupSave extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupSave::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('editGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupSave::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::EDIT_GROUP()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No ID.');
        
        $id = intval($data["id"]);
        $group = Group::getByID($id);
        if(!($group instanceof Group)) $request->send('No Group');
        
        if(isset($data["name"]) && is_string($data["name"])) $group->setName($data["name"]);
        if(isset($data["description"]) && is_string($data["description"])) $group->setDescription ($data["description"]);
        
        $group->update();
        PermissionsAddon::getInstance()->getDatabase()->commitChanges();
        
        
        $request->send($group);
    }
}