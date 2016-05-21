<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserSubmitLogin extends \AjaxRequestHandler {
    private static $INSTANCE;
    public static function getInstance() {
        return UserSubmitLogin::$INSTANCE;
    }
    
    //Instance
    
    public function __construct() {
        parent::__construct('submitLogin');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        
        UserSubmitLogin::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        //Confirm Data
        $data = $request->getData();
        
        //TODO: Add Other supports
        if(!isset($data["username"])) {
            $request->send('Missing Username.');
        }
        
        if(!isset($data["password"])) {
            $request->send('Missing Password.');
        }
        
        $username = $data["username"];
        $password = $data["password"];
        
        //Validate $username
        if(!RegisteredUser::isValidUsername($username)) {
            $request->send('Username invalid.');
        }
        
        //Validate $password
        if(!Password::isValidPassword($password)) {
            $request->send('Password invalid.');
        }
        
        //Check if logged in.
        if(User::isLoggedIn()) {
            $request->send('Already logged in.');
        }
        
        //Now, see if the user actually exists.
        $username_password_bad_response = 'Invalid Username/Password.';
        
        $user = RegisteredUser::getByName($username);
        if(!($user instanceof RegisteredUser)) {
            //Hide Username vs Password errors for security.
            $request->send($username_password_bad_response);
        }
        
        //Try Login
        //TODO: Add support for other login means.
        $password_obj = $user->getPassword();
        if(!($password_obj instanceof Password)) {
            $request->send($username_password_bad_response);
        }
        
        //Check
        $result = $password_obj->compare($password);
        if(!$result) {
            $request->send($username_password_bad_response);
        }
        
        //Login ok, perform the login.
        $login_result = $password_obj->login($password);
        if($login_result) {
            $request->send($user);
        } else {
            $request->send($username_password_bad_response);
        }
    }
}