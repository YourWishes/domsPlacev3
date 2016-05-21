<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

$page = Page::getNewPage();
$page->startPage();

$page->setTitle('Not Found');

$template = $page->getTemplate();
$page->setResponse(HTTPResponse::$NOT_FOUND_404);
$page->echoData($template->genError(404, "Uh-Oh! We couldn't find that. Please check that the file exists and hasn't moved."));

$page->makePage();
$page->endPage();