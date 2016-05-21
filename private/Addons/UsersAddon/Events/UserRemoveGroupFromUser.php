<?php

namespace UsersAddon;

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserRemoveGroupFromUser extends \AjaxRequestHandler {

    private static $INSTANCE;

    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return UserRemoveGroupFromUser::$INSTANCE;
    }

    public function __construct() {
        parent::__construct('removeGroupFromUser');

        //As with most AJAX Request listeners, this self registers
        $this->register();
        UserRemoveGroupFromUser::$INSTANCE = $this;
    }

    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);

        //Check for permissions plugin
        if (UsersAddon::getInstance()->isPermissionsAvailable()) {
            if (!Permissions::REMOVE_GROUP()->canCurrentUser())
                \PermissionsAddon\Permission::noPermissionResponse($request);
        }

        $data = $request->getData();
        if (!isset($data["id"]) || !is_numeric($data["id"]))
            $request->send('Missing User.');
        if (!isset($data["group"]) || !is_numeric($data["group"]))
            $request->send('Missing Group.');

        $user_id = intval($data["id"]);
        $group_id = intval($data["group"]);

        //Try Get User
        $user = User::getByID($user_id);
        if (!($user instanceof \UsersAddon\User))
            $request->send("Invalid User.");

        //Try Get Group
        $group = \PermissionsAddon\Group::getByID($group_id);
        if (!($group instanceof \PermissionsAddon\Group))
            $request->send("Invalid Group.");

        //Check if user is already part of the group
        $groups = $user->getGroups();
        if (!$groups->contains($group))
            $request->send("User not apart of group.");

        $group->removeUser($user);
        \PermissionsAddon\Group::getTable()->database->commitChanges();

        $request->send($group);
    }

}
