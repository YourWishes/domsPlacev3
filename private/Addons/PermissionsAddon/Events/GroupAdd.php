<?php
namespace PermissionsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class GroupAdd extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return GroupAdd::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('addGroup');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        GroupAdd::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        if(!Permissions::ADD_GROUP()->canCurrentUser()) Permission::noPermissionResponse ($request);
        
        $data = $request->getData();
        if(!isset($data["name"]) || !is_string($data["name"])) $request->send('Missing name.');
        if(!isset($data["description"]) || !is_string($data["description"])) $request->send('Missing description.');
        
        //Try Get Group
        $group = Group::getByName($data["name"]);
        if($group instanceof Group) $request->send('Group already exists.');
        
        $group = new Group();
        $group->setName($data["name"]);
        $group->setDescription($data["description"]);
        $entry = $group->createEntry();
        $entry->getTable()->database->commitChanges();
        $idf = Group::getIDField();
        
        $id = $entry->getData()->get($idf);
        $request->send(Group::getByID($id));
    }
}