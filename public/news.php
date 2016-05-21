<?php
$currentpage = __FILE__;
require_once('../private/Main.php');//MUST be imported.
import('Page.Page');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

//First, check the ID exists and is valid (integer)
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $template->importPage('NotFound');
}

//Try get the news post by its id
$post = domsPlaceAddon\News::getByID(intval($_GET['id']));
if(!($post instanceof domsPlaceAddon\News)) {
    $template->importPage('NotFound');
}

$page->setTitle($post->getTitle());
$page->echoData('<h1>'.$template->escapeHTML($post->getTitle()).'</h1>');

//I'm a smart fucka so the news here is going to be imported from the template

$component = $template->getComponent('NewsSmall');
$page->echoData($component->generate($template, $page, $post));

$page->echoData('<h1>Comments <br /><small>Returning soon!</small></h1>');

$page->makePage();
$page->endPage();