<?php
$currentpage = __FILE__;
require_once('../private/Main.php');//MUST be imported.
import('Page.Page');
import('Ajax.AjaxRequest');

$page = Page::getPage();
$page->startPage();
$template = $page->getTemplate();
$page->setTitle('About me');

$dom = domsPlaceAddon\domsPlaceAddon::getUserDominic();

$page->echoData('<h1>About me</h1>');
$page->echoData('<div class="row">');{
    $page->echoData('<div class="col-xs-3">');
    $x = '<img src="' . $dom->getImage()->getAsHTTPPath() . '" class="center-block" alt="Dominic" title="Dominic Masters" />';
    $page->echoData($template->generatePanel($x));
    $page->echoData('</div>');
    
    $page->echoData('<div class="col-xs-9">');
    $x = domsPlaceAddon\domsPlaceAddon::getInstance()->getFolder()->getChild('about.txt')->getFileContents();
    $page->echoData($template->generatePanel($x));
    $page->echoData('</div>');
}
$page->echoData('</div>');

    
//Generate our form here
$page->echoData('<div class="row">');{
    $page->echoData('<div class="col-sm-12">');
    $page->echoData('<h1 id="contact">Contact Me</h1>');
    $form = new FormsAddon\Form();
    $name = new \FormsAddon\FormControl('name', FormsAddon\FormControlType::$TEXT);
    $name->placeholder = "Enter your name.";
    $name->label = "Your Name";
    $name->required = true;
    $name->title = "Your name, either display name or real name.";

    $email = new \FormsAddon\FormControl('email', FormsAddon\FormControlType::$EMAIL);
    $email->placeholder = "Enter your email address.";
    $email->label = "Your Email";
    $email->required = true;
    $email->title = "Your email, I will reply to this address.";

    $body = new FormsAddon\FormControl('message', FormsAddon\FormControlType::$TEXTAREA);
    $body->placeholder = "Enter a short message to send to me.";
    $body->label = "Message";
    $body->required = true;
    $body->title = "Enter your short message here, I will recieve this and be able to answer best if you are clear and concise.";
    
    $send = new \FormsAddon\FormControl('send', FormsAddon\FormControlType::$SUBMIT);
    $send->value = "Send!";
    
    $form->addControl($name)->addControl($email)->addControl($body)->addControl($send);
    $form->setAction(\domsPlaceAddon\SendEmail::getInstance());

    $page->echoData($template->generatePanel($template->getComponent('Form')->generate($template, $page, $form)));
    $page->echoData('</div>');
};
$page->echoData('</div>');
$page->echoData('<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '">require("Template.About.Contact");</script>');

//End Page Processing
$page->makePage();
$page->endPage();