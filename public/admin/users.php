<?php
$currentpage = __FILE__;
require_once('../../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

if(!\UsersAddon\Permissions::VIEW_USERS()->canCurrentUser()) {
    $template->importPage('NoPermission');
}

$page->setTitle('Manage Users');

$page->echoData('<h1>Manage Users</h1>');

$x = '<table class="table table-condensed table-hover">';
$x .= '<thead>';
$x .= '<tr><th>ID</th><th>Name</th><th>Email</th><th>&nbsp;</th></tr>';
$x .= '</thead>';
$x .= '<tbody>';

//Get all registered users
$users = UsersAddon\RegisteredUser::getAll();
foreach($users as $user) {
    if(!($user instanceof \UsersAddon\RegisteredUser)) continue;
    $x .= '<tr>';
    $x .= '<td>' . $user->getID() . '</td>';
    $x .= '<td><a href="'.File::getTopDirectoryAsHTTP().'/user/view?id='.$user->getID().'">' . $template->escapeHTML($user->getUsername()) . '</a></td>';
    $x .= '<td><a href="mailto:'.$user->getEmail().'">' . $template->escapeHTML($user->getEmail()) . '</a></td>';
    $x .= '<td>';
    $needs_space = true;//For IE fix.
    if(UsersAddon\Permissions::SET_PASSWORD()->canCurrentUser()) {
        $needs_space = false;
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/admin/user/password?id='.$user->getID().'" class="btn btn-default btn-xs">Change Password</a>';
    }
    if($needs_space) $x .= '&nbsp;';
    $x .= '</td>';
    $x .= '</tr>';
}

$x .= '</tbody>';
$x .= '</table>';

$page->echoData($template->generatePanel($x, 'Users'));

//End Page Processing
$page->makePage();
$page->endPage();