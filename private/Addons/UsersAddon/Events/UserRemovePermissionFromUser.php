<?php

namespace UsersAddon;

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');

class UserRemovePermissionFromUser extends \AjaxRequestHandler {

    private static $INSTANCE;

    /**
     * 
     * @return UserAdd
     */
    public static function getInstance() {
        return UserRemovePermissionFromUser::$INSTANCE;
    }

    public function __construct() {
        parent::__construct('removePermissionFromUser');

        //As with most AJAX Request listeners, this self registers
        $this->register();
        UserRemovePermissionFromUser::$INSTANCE = $this;
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
        if (!isset($data["permission"]) || !is_numeric($data["permission"]))
            $request->send('Missing Permission.');

        $user_id = intval($data["id"]);
        $permission_id = intval($data["permission"]);

        //Try Get User
        $user = User::getByID($user_id);
        if (!($user instanceof \UsersAddon\User))
            $request->send("Invalid User.");

        //Try Get Group
        $permission = \PermissionsAddon\Permission::getByID($permission_id);
        if (!($permission instanceof \PermissionsAddon\Permission))
            $request->send("Invalid Permission.");

        //Check if user is already part of the group
        $perms = \PermissionsAddon\Permission::getUsersPermissions($user);
        if (!$perms->contains($permission))
            $request->send("User does not have permission.");

        $permission->removeFromUser($user);
        \PermissionsAddon\Permission::getTable()->database->commitChanges();

        $request->send($permission);
    }

}
