<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('NewsSmall', function(&$template, &$page, $data) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    if(!($data instanceof domsPlaceAddon\News)) throw new Exception ();
    //Generate Code Here
    $postLog = $data->getPostedLog();
    $user = $postLog->getPoster();
    
    //Now the HTML
    $x = '';//Here is our StringBuffer for the header code.
    
    $x .= '<div class="profile">';
    $x .= $template->getComponent('UserImage')->generate($template, $page, $user);
    $x .= '</div>';
    $x .= '<div>';
    $x .= $data->getBody();
    $x .= '</div>';
    $footer = 'Posted '.$template->formatTime($data->getTime());
    if($postLog instanceof domsPlaceAddon\NewsPostedLog) {
        $user = $postLog->getPoster();
        if($user instanceof UsersAddon\RegisteredUser) {
            $footer .= ' by <a href="'.File::getTopDirectoryAsHTTP().'/user/view?id='.$user->getID().'">' . $user->getUsername() . '</a>';
        }
    }
    
    $title = '';
    $title .= '<div class="panel-buttons">';
    if($data->canCurrentUserEdit()) {
        $title .= '<a href="' . File::getTopDirectoryAsHTTP() . '/admin/news/edit?id='.$data->getID().'" class="btn btn-warning">';
        $title .= $template->generateIcon('pencil');
        $title .= '</a>';
    }
    if($data->canCurrentUserDelete()) {
        $title .= '<a href="' . File::getTopDirectoryAsHTTP() . '/admin/news/delete?id='.$data->getID().'" class="btn btn-danger">';
        $title .= $template->generateIcon('trash-o');
        $title .= '</a>';
    }
    $title .= '</div>';
    
    $title .= '<a href="'.File::getTopDirectoryAsHTTP().'/news?id='.$data->getID().'">' . $template->escapeHTML($data->getTitle()) . '</a>';
    
    $x = $template->generatePanel($x, $title, $footer);
    
    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
});