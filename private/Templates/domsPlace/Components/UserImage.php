<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('UserImage', function(&$template, &$page, $user) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    if(!($user instanceof UsersAddon\User)) throw new Exception ();
    
    $reg_user = $user->isRegistered();
    $reg = $reg_user instanceof UsersAddon\RegisteredUser;
    $x = '<a href="'.File::getTopDirectoryAsHTTP().'/user/view?id='.$user->getID().'">';
    $x .= '<img src="'.$user->getImage()->getAsHTTPPath().'" width="128" height="128" title="'.($reg ? $reg_user->getUsername() : 'User').'" />';
    $x .= '</a>';
    
    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
});