<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupDelete extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupDelete::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('deleteGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupDelete::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::DELETE_GROUP()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send ('No ID.');
        
        $id = intval($data["id"]);
        $group = Group::getByID($id);
        if(!($group instanceof Group)) $request->send('No Group');
        
        
        
        try {
            $group->delete();
            PermissionsAddon::getInstance()->getDatabase()->commitChanges();
        } catch(\Exception $e) {
            $request->send($e->getMessage());
        }
        $request->send(true);
    }
}