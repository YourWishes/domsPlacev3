<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserSubmitRegistration extends \AjaxRequestHandler {
    private static $INSTANCE;
    public static function getInstance() {
        return UserSubmitRegistration::$INSTANCE;
    }
    
    //Instance
    
    public function __construct() {
        parent::__construct('submitRegistration');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        
        UserSubmitRegistration::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        //Confirm Data
        $data = $request->getData();
        if(!isset($data['username'])) $request->send('Missing Username.');
        if(!isset($data['password'])) $request->send('Missing Password.');
        if(!isset($data['email'])) $request->send('Missing Email.');
        if(!isset($data['terms'])) $request->send('You must agree to the terms and conditions.');
        
        $username = $data["username"];
        $pwd = $data["password"];
        $email = $data["email"];
        $terms = $data["terms"];
        
        if(!\FormsAddon\FormControl::isDataChecked($terms)) $request->send('You must agree to the terms and conditions.');
        if(!\Email::isValidEmail($email)) $request->send('Invalid Email.');
        if(!Password::isValidPassword($pwd)) $request->send('Invalid Password.');
        
        //First, make sure email isn't in system.
        if(RegisteredUser::getByEmail($email) instanceof RegisteredUser) $request->send('Email already in use.');
        if(RegisteredUser::getByName($username) instanceof RegisteredUser) $request->send('Username already in use.');
        
        //Try to generate a password (IMPORTANT)
        try {
            $user = RegisteredUser::registerNewUser($username, $email);
            $password = Password::generateForUser($user, $pwd);
            $password->createEntry();
            Password::getTable()->database->commitChanges();
            $password->login($pwd);
            
            $event = new UserRegisterEvent($user);
            $event->fire();
        } catch(\Exception $e) {
            $request->send('An internal error occured, if problems persist please contact us to assist.');
        }
        
        //TODO: Finish
        $request->send($user);
    }
}