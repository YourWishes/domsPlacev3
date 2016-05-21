<?php

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Header', function(&$template, &$page, $data) {
    if (!($template instanceof domsPlace))
        throw new Exception();
    if (!($page instanceof Page))
        throw new Exception ();
    //Generate Code Here
    //Now the HTML
    $x = ''; //Here is our StringBuffer for the header code.
    //First print the Document type, at the time of writing this comment it's HTML5
    $x .= '<!DOCTYPE HTML>';
    $x .= '<html lang="' . $page->getLanguage()->getLanguageCode() . '">';

    //Begin the Page Header
    $x .= '<head>';
    
    //META Tags
    {
        $x .= '<meta charset="' . $page->getCharset()->getName() . '" />';
        if (!$page->getTags()->isEmpty()) $x .= '<meta name="keywords" content="' . $page->getTags()->implode() . '"/>';
        $x .= '<meta name="language" content="' . $page->getLanguage()->getLanguageCode() . '">';
        if (!$page->cachePage) $x .= '<meta http-equiv="Cache-Control" content="no-cache">';
        $x .= '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
        $x .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }

    //Title
    if ($page->getTitle() === null) {
        $x .= '<title>' . getconf('SEO_TITLE') . '</title>';
    } else {
        $x .= '<title>' . $page->getTitlePrefix() . $page->getTitle() . $page->getTitlePostfix() . '</title>';
    }
    
    //CSS
    {
        $css = array(
            "font-awesome.min.css",
            "flipps.css",
            "bootstrap.min.css"
        );
        foreach ($css as $css_file) {
            $f = File::getDocumentRoot()->getChild('css')->getChild($css_file);
            $x .= '<link rel="stylesheet" type="' . ContentType::$TEXT_CSS->getMIMEType() . '" href="' . $f->getAsHTTPPath() . '" />';
        }
        $x .= '<style type="' . ContentType::$TEXT_CSS->getMIMEType() . '" />' . $template->getFile('Stylesheets')->getChild('css')->getChild('main.css')->getFileContents() . '</style>';
    }
    
    //JS
    {
        $scripts = array(
            "jquery.min.js",
            "bootstrap.min.js"
        );
        foreach ($scripts as $css_file) {
            $f = File::getDocumentRoot()->getChild('js')->getChild($css_file);
            $x .= '<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '" src="' . $f->getAsHTTPPath() . '"></script>';
        }
        $x .= '<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '" />' . Page::getMainJavascript() . '</script>';
        $x .= '<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '">' . $template->getFile('Javascript')->getChild('Main.js')->getFileContents() . '</script>';
    }

    $x .= $template->favicon->generate($template, $page, $x);
    
    $x .= '</head>';
    //End Page Header
    //Start Page Body (See Footer.php for the closing tags)
    $x .= NEWLINE;
    $x .= '<body>';

    //Navbar
    $x .= $template->navbar->generate($template, $page, $data);

    $x .= '<div class="container">';

    $x .= '<div class="row">';
    $x .= '<div class="col-xs-12 body-col">';

    return $x; //Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if (!($template instanceof domsPlace))
        throw new Exception('Invalid Template.');
    $template->navbar = $template->importComponent('Navbar');
    $template->favicon = $template->importComponent('Favicon');
});
