<?php

$currentpage = __FILE__;
require_once('../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();
$page->setTitle('Website Policy');

$updated = new DateTime('2016-02-27 18:54:00 GMT+9:30');

$page->echoData('<h1>Website Policy/Terms of Use<br /><small>Last Updated '.$template->formatTime($updated).'</small></h1>');

$page->echoData('<div class="row">');
{
    
    /*$page->echoData('<div class="col-sm-3">');
    
    //Generate our href panel
    $x = '';
    $x .= '<a href="#">Hello World</a>';
    $page->echoData($template->generatePanel($x, 'Quick Navigate'));
    $page->echoData('</div>');*/
    
    $page->echoData('<div class="col-sm-12">');
    
    //Generate our href panel
    $x = domsPlaceAddon\domsPlaceAddon::getInstance()->getFolder()->getChild('privacy.html')->getFileContents();
    $page->echoData($template->generatePanel($x, 'Privacy Policy'));
    
    $page->echoData('</div>');
}
$page->echoData('</div>');


//End Page Processing
$page->makePage();
$page->endPage();
