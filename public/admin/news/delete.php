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

if(!$post->canCurrentUserDelete()) {
    $template->importPage('NoPermission');
}

if(isset($_GET['confirm']) && $_GET['confirm'] == true) {
    $post->hide();
    $page->setTitle('Post Deleted.');
    
    $x = 'Post deleted. <a href="'.File::getTopDirectoryAsHTTP().'/">Click here to go home</a>';
    $y = 'Post Deleted.';
    $page->echoData($template->generatePanel($x, $y));
    \domsPlaceAddon\News::getTable()->database->commitChanges();
    
    $page->makePage();
    $page->endPage();
}

$page->setTitle('Delete Post #' . $post->getID());


$page->echoData('<div class="row">');
$page->echoData('<div class="col-xs-12 col-sm-8 col-sm-offset-2">');{
    $x = 'Are you sure you wish to delete the following post?';
    $y = 'Delete Post #' . $post->getID();
    $z = $template->generateYesNoButtons('/admin/news/delete?id='.$post->getID().'&confirm=true', '/news?id='.$post->getID());
    $page->echoData($template->generatePanel($x, $y, $z));
}
$page->echoData('</div>');
$page->echoData('</div>');

$page->echoData('<div class="row">');
$page->echoData('<div class="col-xs-12">');{
    $page->echoData($template->getComponent('NewsSmall')->generate($template, $page, $post));
}
$page->echoData('</div>');
$page->echoData('</div>');

$page->makePage();
$page->endPage();