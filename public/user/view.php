<?php
$currentpage = __FILE__;
require_once('../../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $template->importPage('NotFound');
}

$id = intval($_GET['id']);
$user = UsersAddon\RegisteredUser::getByID($id);
if(!($user instanceof \UsersAddon\RegisteredUser)) {
    $template->importPage('NotFound');
}

$page->setTitle('Viewing ' . $user->getUsername() . '\'s Profile');

$page->echoData('<h1>Viewing ' . $template->escapeHTML($user->getUsername()) . '\'s Profile</h1>');

//End Page Processing
$page->makePage();
$page->endPage();