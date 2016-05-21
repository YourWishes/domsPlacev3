<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('ProjectSmall', function(&$template, &$page, $data) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    if(!($data instanceof domsPlaceAddon\Project)) throw new Exception ();
    
    //Now the HTML
    $x = '<div class="col-sm-4">';
    $ban = $data->getBanner();
    
    $h = '<a href="'.File::getTopDirectoryAsHTTP().'/Projects/View/?id='.$data->getID().'" title="'.$data->getTitle().'">';
    $h .= '<img src="' . $ban->getAsHTTPPath() . '" class="img-responsive" />';
    $h .= '</a>';
    
    $x .= $template->generatePanel($h, null, null, array("panel-default", "panel-nomargin"));
    $x .= '</div>';
    
    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
});