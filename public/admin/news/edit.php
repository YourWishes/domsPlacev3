<?php
$currentpage = __FILE__;
require_once('../../../private/Main.php');//MUST be imported.
import('Page.Page');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

//First, check the ID exists and is valid (integer)
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $template->importPage('NotFound');
}

//Try get the news post by
//0 its id
$post = domsPlaceAddon\News::getByID(intval($_GET['id']));
if(!($post instanceof domsPlaceAddon\News)) {
    $template->importPage('NotFound');
}

if(!$post->canCurrentUserEdit()) {
    $template->importPage('NoPermission');
}

$page->echoData('<h1>Edit news post</h1>');
$page->setTitle('Edit news post');

$form = \domsPlaceAddon\News::getForm($post);
$x = $template->importComponent('Form')->generate($template, $page, $form);
$x = $template->generatePanel($x, 'Edit news post');

$page->echoData($x);

$page->echoData('<script type="'.ContentType::$TEXT_JAVASCRIPT->getMIMEType().'">require("Template.Admin.News");</script>');

$page->makePage();
$page->endPage();