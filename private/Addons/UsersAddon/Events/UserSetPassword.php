<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserSetPassword extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return UserSetPassword::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('setPassword');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        UserSetPassword::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        //Check for permissions plugin
        if(UsersAddon::getInstance()->isPermissionsAvailable()) {
            if(!Permissions::SET_PASSWORD()->canCurrentUser()) \PermissionsAddon\Permission::noPermissionResponse($request);
        }
        
        $data = $request->getData();
        
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send('Not a valid ID.');
        if(!isset($data["password"]) || !Password::isValidPassword($data["password"])) $request->send ('Not a valid password.');
        
        $id = intval($data["id"]);
        $raw = $data["password"];
        
        $user = \UsersAddon\User::getByID($id);
        if(!($user instanceof User)) $request->send('No User.');
        
        $reg = $user->isRegistered();
        if(!($reg instanceof RegisteredUser)) $request->send('Invalid user.');
        
        $psw = $user->getPassword();
        if(!($psw instanceof Password)) $request->send('Not Password.');
        
        $new_psw = Password::generateForUser($reg, $raw);
        
        $psw->setTime(new \DateTime());
        $fld = Password::getTable()->getField('password');
        $psw->setField($fld, $new_psw->getValue($fld));
        $fld = Password::getTable()->getField('salt');
        $psw->setField($fld, $new_psw->getValue($fld));
        
        $psw->update();
        Password::getTable()->database->commitChanges();
        
        $request->send(true);
    }
}