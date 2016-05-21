<?php
$currentpage = __FILE__;
require_once('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'System'.DIRECTORY_SEPARATOR.'Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $template->importPage('NotFound');
}

$id = intval($_GET['id']);
$project = \domsPlaceAddon\Project::getByID($id);
if(!($project instanceof \domsPlaceAddon\Project)) {
    $template->importPage('NotFound');
}

$page->setTitle($project->getTitle());
$page->echoData('<h1>' . $template->escapeHTML($project->getTitle()) . '</h1>');

$page->echoData('<div class="row">');
$page->echoData('<div class="col-sm-4">');
$x = '';
$x .= '<img src="'.$project->getBanner()->getAsHTTPPath().'" class="img-responsive" />';
$x .= '<table class="table table-condensed">';
$x .= '<tr><th>Project Title</th><td>'.$template->escapeHTML($project->getTitle()).'</td></tr>';
$x .= '</table>';
$page->echoData($template->generatePanel($x, "Details", null, array("panel-default")));
$page->echoData('</div>');

$page->echoData('<div class="col-sm-8">');
$x = $project->getDescription();//Warning! Descriptions are NOT escaped!!!
$page->echoData($template->generatePanel($x, "Description"));
$page->echoData('</div>');

$page->echoData('</div>');

//End Page Processing
$page->makePage();
$page->endPage();