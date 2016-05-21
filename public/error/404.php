<?php
$currentpage = __FILE__;
require_once('../../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->getTemplate()->importPage('NotFound');