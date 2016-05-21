<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserRemove extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserRemove
     */
    public static function getInstance() {
        return UserRemove::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('removeUser');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        UserRemove::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        if(UsersAddon::getInstance()->isPermissionsAvailable()) {
            if(!Permissions::REMOVE_USER()->canCurrentUser()) \PermissionsAddon\Permission::noPermissionResponse($request);
        }
        
        $data = $request->getData();
        
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send('No User ID.');
        
        $id = intval($data["id"]);
        
        $user = User::getByID($id);
        if(!($user instanceof User)) $request->send('User doesn\'t exist.');
        
        //Prepare our event...
        UsersAddon::getInstance()->import('Event.onUserDeleted');
        $evt = new \onUserDeleted($user);
        $evt->fire();
        $user->delete();
        
        $request->send('Not Yet Implemented.');
    }
}