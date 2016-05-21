<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Footer', function(&$template, &$page, $data) {
    if(!($template instanceof Resume)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    //Generate Code Here
    
    $x = '';
    
    //Cuz I'm such a fuckin' nerd here's the stats.
    $x .= '<!--' . NEWLINE;
    $x .= 'Page Processing Took: ' . (microtime(true)-SCRIPT_LOADTIME) . 's' . NEWLINE;
    $x .= '-->';
    
    $x .= '</body>';
    $x .= '</html>';
    
    return $x;//Finally return the string buffer
}, function() {
    //Construct Code Here
});