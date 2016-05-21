<?php
$currentpage = __FILE__;
require_once('../../../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

if(!\domsPlaceAddon\Permissions::CREATE_NEWS_POST()->canCurrentUser()) {
    $template->importPage('NoPermission');
}

$page->echoData('<h1>Create news post</h1>');
$page->setTitle('Create news post');

$form = \domsPlaceAddon\News::getBlankForm();
$x = $template->importComponent('Form')->generate($template, $page, $form);
$x = $template->generatePanel($x, 'Create news post');

$page->echoData($x);

$page->echoData('<script type="'.ContentType::$TEXT_JAVASCRIPT->getMIMEType().'">require("Template.Admin.News");</script>');

//End Page Processing
$page->makePage();
$page->endPage();