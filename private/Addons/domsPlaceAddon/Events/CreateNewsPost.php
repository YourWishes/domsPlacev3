<?php
namespace domsPlaceAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');
import('Email.Email');

class CreateNewsPost extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return SendEmail
     */
    public static function getInstance() {
        return static::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('createNewsPost');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        static::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        if(!Permissions::CREATE_NEWS_POST()->canCurrentUser()) \PermissionsAddon\Permission::noPermissionResponse($request);
        
        $data = $request->getData();
        if(!isset($data["title"])) $request->send('Missing Title');
        if(!isset($data["body"])) $request->send('Missing Body');
        
        
        $title = $data["title"];
        if(strlen($title) > News::getTable()->getField('title')->max_length) $request->send('Title too long.');
        if(str_replace(' ', '', $title) == '') $request->send('Missing Title');
        
        $body = $data["body"];
        if(str_replace(' ', '', $body) == '') $request->send('Missing Body');
        
        //OK Since $body can infact be RAW html, we need to check for a few things...
        //$body = domsPlaceAddon::getInstance()->formatHTML($body);
        //..end check
        
        $post = new News();
        $post->setTitle($title);
        $post->setBody($body);
        try {
            $post->postNews(\UsersAddon\User::getLoggedInUser());
        } catch(\Exception $e) {
            $request->send($e->getMessage());
        }
        $request->send($post);
    }
}