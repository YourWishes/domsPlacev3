<?php
namespace domsPlaceAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');
import('Email.Email');

class EditNewsPost extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return EditNewsPost
     */
    public static function getInstance() {
        return static::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('editNewsPost');
        
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
        $data = $request->getData();
        
        if(!isset($data["id"]) || !is_numeric($data["id"])) $request->send('Missing Post ID');
        if(!isset($data["title"])) $request->send('Missing Title');
        if(!isset($data["body"])) $request->send('Missing Body');
        
        $id = intval($data["id"]);
        $post = News::getByID($id);
        if(!($post instanceof News)) $request->send('Missing Post');
        if(!$post->canCurrentUserEdit()) \PermissionsAddon\Permission::noPermissionResponse($request);
        
        $title = $data["title"];
        if(strlen($title) > News::getTable()->getField('title')->max_length) $request->send('Title too long.');
        if(str_replace(' ', '', $title) == '') $request->send('Missing Title');
        
        $body = $data["body"];
        if(str_replace(' ', '', $body) == '') $request->send('Missing Body');
        
        $post->setTitle($title);
        $post->setBody($body);
        try {
            $post->update();
            News::getTable()->database->commitChanges();
        } catch(\Exception $e) {
            $request->send($e->getMessage());
        }
        $request->send($post);
    }
}