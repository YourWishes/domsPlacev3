<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Header', function(&$template, &$page, $data) {
    if(!($template instanceof Resume)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    //Generate Code Here
    
    //Now the HTML
    $x = '';//Here is our StringBuffer for the header code.
    
    //First print the Document type, at the time of writing this comment it's HTML5
    $x .= '<!DOCTYPE HTML>';
    $x .= '<html lang="'.$page->getLanguage()->getLanguageCode().'">';
    
    //Begin the Page Header
    $x .= '<head>';
    $x .= '<meta charset="'.$page->getCharset()->getName().'" />';
    if(!$page->getTags()->isEmpty()) $x .= '<meta name="keywords" content="'.$page->getTags()->implode().'"/>';
    $x .= '<meta name="language" content="'.$page->getLanguage()->getLanguageCode().'">';
    if(!$page->cachePage) $x .= '<meta http-equiv="Cache-Control" content="no-cache">';
    
    $x .= '<title>' . $page->getTitlePrefix() . $page->getTitle() . $page->getTitlePostfix() . '</title>';
    
    //CSS
    {
        $css = array(
            "font-awesome.min.css",
            "flipps.css"
            //"bootstrap.min.css"
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
            "jquery.min.js"
            //"bootstrap.min.js"
        );
        foreach ($scripts as $css_file) {
            $f = File::getDocumentRoot()->getChild('js')->getChild($css_file);
            $x .= '<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '" src="' . $f->getAsHTTPPath() . '"></script>';
        }
        $x .= '<script type="' . ContentType::$TEXT_JAVASCRIPT->getMIMEType() . '" />' . Page::getMainJavascript() . '</script>';
    }
    
    $x .= '</head>';
    //End Page Header
    
    //Start Page Body (See Footer.php for the closing tags)
    $x .= NEWLINE;
    $x .= '<body>';

    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
});