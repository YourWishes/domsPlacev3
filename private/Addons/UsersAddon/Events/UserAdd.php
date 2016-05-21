<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserAdd extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return UserAdd::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('addUser');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        UserAdd::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        //Check for permissions plugin
        if(UsersAddon::getInstance()->isPermissionsAvailable()) {
            if(!Permissions::ADD_USER()->canCurrentUser()) \PermissionsAddon\Permission::noPermissionResponse($request);
        }
        
        $data = $request->getData();
        
        if(!isset($data["username"]) || !RegisteredUser::isValidUsername($data["username"])) $request->send('Invalid Username.');
        if(!isset($data["password"]) || !Password::isValidPassword($data["password"])) $request->send('Invalid Password.');
        if(!isset($data["email"]) || !\Email::isValidEmail($data["email"])) $request->send('Invalid Email.');
        
        //Try get user by name
        
        //Create a gnu user.
        try {
            $user = RegisteredUser::registerNewUser($data["username"], $data["email"]);
            $password = Password::generateForUser($user, $data["password"]);
            $entry = $password->createEntry();
            $entry->getTable()->database->commitChanges();
        } catch(\Exception $e) {
            $request->send($e->getMessage());
        }
        
        $request->send($user);
    }
}