<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Favicon', function(&$template, &$page, $user) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    
    $favicon_folder = File::getTopDirectory()->getChild('favicon');
    
    $x = '';
    
    /*$x .= '<link rel="apple-touch-icon" sizes="57x57" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-57x57.png">';
    $x .= '<link rel="apple-touch-icon" sizes="60x60" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-60x60.png">';
    $x .= '<link rel="apple-touch-icon" sizes="72x72" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-72x72.png">';
    $x .= '<link rel="apple-touch-icon" sizes="76x76" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-76x76.png">';
    $x .= '<link rel="apple-touch-icon" sizes="114x114" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-114x114.png">';
    $x .= '<link rel="apple-touch-icon" sizes="120x120" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-120x120.png">';
    $x .= '<link rel="apple-touch-icon" sizes="144x144" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-144x144.png">';
    $x .= '<link rel="apple-touch-icon" sizes="152x152" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-152x152.png">';
    $x .= '<link rel="apple-touch-icon" sizes="180x180" href="'.$favicon_folder->getAsHTTPPath().'/apple-icon-180x180.png">';
    $x .= '<link rel="icon" type="image/png" sizes="192x192"  href="'.$favicon_folder->getAsHTTPPath().'/android-icon-192x192.png">';*/
    $x .= '<link rel="icon" type="image/png" sizes="32x32" href="'.$favicon_folder->getAsHTTPPath().'/favicon-32x32.png">';
    $x .= '<link rel="icon" type="image/png" sizes="16x16" href="'.$favicon_folder->getAsHTTPPath().'/favicon-16x16.png">';
    
    return $x;
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
});