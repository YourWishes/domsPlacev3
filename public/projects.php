<?php
$currentpage = __FILE__;
require_once('../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$page->setTitle('Projects');
$template = $page->getTemplate();

$page->echoData('<h1>Projects</h1>');
$projects = domsPlaceAddon\Project::getAll();
$projects->sortByFunctionValue('getTitle', array(), true);

$component = $template->getComponent('ProjectSmall');
$index = 0;
$open_row = false;
foreach($projects as $project) {
    if($index == 0) {
        $page->echoData('<div class="row">');
        $open_row = true;
    }
    $page->echoData($component->generate($template, $page, $project));
    if($index == 2) {
        $page->echoData('</div>');
        $index = 0;
        $open_row = false;
    }
    $index++;
}
if($open_row) $page->echoData('</div>');

//End Page Processing
$page->makePage();
$page->endPage();