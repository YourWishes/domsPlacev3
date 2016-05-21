<?php
$currentpage = __FILE__;
require_once('../../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

if(!\PermissionsAddon\Permissions::VIEW_ADMIN_PANEL()->canCurrentUser()) {
    $template->importPage('NoPermission');
}

$page->setTitle('Admin Panel');

/**
 * Constants for the page
 */
$users_count = \UsersAddon\User::getCount();
$registered_users_count = \UsersAddon\RegisteredUser::getCount();

$news_count = \domsPlaceAddon\News::getCount();

$page->echoData('<h1 class="page-title">Administrator Panel</h1>');
$page->echoData('<div class="row">');{
    /**
     * Manage Users
     */
    $page->echoData('<div class="col-sm-6">');
    $page->echoData('<div class="list-group">');
    $page->echoData('<div class="list-group-item disabled">Manage Users</div>');
    if(UsersAddon\Permissions::VIEW_USERS()->canCurrentUser()) $page->echoData('<a href="'.File::getTopDirectoryAsHTTP().'/admin/users" class="list-group-item">Manage Users</a>');
    if(PermissionsAddon\Permissions::VIEW_PERMISSIONS()->canCurrentUser()) $page->echoData('<a href="'.File::getTopDirectoryAsHTTP().'/Admin/Permissions/" class="list-group-item">Manage Permissions</a>');
    if(UsersAddon\Permissions::ADD_USER()->canCurrentUser()) $page->echoData('<a href="'.File::getTopDirectoryAsHTTP().'/Admin/Users/Add/" class="list-group-item">Create New User</a>');
    
    $page->echoData('<div class="list-group-item disabled">Currently Tracking '.$users_count.' users ('.$registered_users_count.' registered)</div>');
    $page->echoData('</div>');
    $page->echoData('</div>');
    
    /**
     * Manage News
     */
    $page->echoData('<div class="col-sm-6">');
    $page->echoData('<div class="list-group">');
    $page->echoData('<div class="list-group-item disabled">Manage News</div>');
    if(\domsPlaceAddon\Permissions::CREATE_NEWS_POST()->canCurrentUser()) $page->echoData('<a href="'.File::getTopDirectoryAsHTTP().'/admin/news/post" class="list-group-item">Create Post</a>');
    
    $page->echoData('<div class="list-group-item disabled">Currently Tracking '.$news_count.' news posts.</div>');
    $page->echoData('</div>');
    $page->echoData('</div>');
}
$page->echoData('</div>');

//End Page Processing
$page->makePage();
$page->endPage();