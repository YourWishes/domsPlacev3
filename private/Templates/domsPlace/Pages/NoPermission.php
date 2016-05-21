<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

$page = Page::getNewPage();
$page->startPage();
$page->setTitle('No Permission');

$x = 'You do not have permission to access this.';
$x .= $page->getTemplate()->generateNewLine();
if(!\UsersAddon\User::isLoggedIn()) {
    $x .= 'If you believe this is an error, you can try <a href="/User/Login">Clicking Here</a> to login.';
} else {
    $x .= 'You can <a href="/User/Welcome">Click Here</a> to return to the dashboard.';
    $usr = UsersAddon\User::getLoggedInUser();
}

$page->echoData($page->getTemplate()->generatePanel($x, 'No Permission!'));
$page->makePage();
$page->endPage();