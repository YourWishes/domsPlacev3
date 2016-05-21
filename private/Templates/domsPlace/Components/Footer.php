<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Footer', function(&$template, &$page, $data) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    //Generate Code Here
    
    $x = '';
    
    $x .= '</div>';
    $x .= '</div>';
    
    $x .= '<div class="row">';
    $x .='<div class="col-xs-12">';
    $x .= '<div class="panel panel-default">';
    $x .= '<div class="panel-body text-center footer">';
    {
        $x .= '<div>';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/">Home</a>';
        $x .= ' | ';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/projects">Projects</a>';
        $x .= ' | ';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/policy">Website Policy</a>';
        $x .= ' | ';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/about">About Me</a>';
        $x .= ' | ';
        $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/about#contact">Contact Me</a>';
        
        $x .= '</div>';
    }
    {
        $x .= '<div class="social">';
        $x .= '<a href="'.getconf('GITHUB_URL').'">'.$template->generateIcon('github').'</a>';
        $x .= '<a href="https://twitter.com/'.getconf('TWITTER').'">'.$template->generateIcon('twitter').'</a>';
        $x .= '<a href="'.getconf('STEAM_URL').'">'.$template->generateIcon('steam-square').'</a>';
        $x .= '<a href="'.getconf('LINKEDIN_URL').'">'.$template->generateIcon('linkedin-square').'</a>';
        $x .= '<a href="'.getconf('BITBUCKET_URL').'">'.$template->generateIcon('bitbucket').'</a>';
        $x .= '<a href="'.getconf('YOUTUBE_URL').'">'.$template->generateIcon('youtube').'</a>';
        $x .= '<a href="'.getconf('TWITCH_URL').'">'.$template->generateIcon('twitch').'</a>';
        $x .= '<a href="'.getconf('GOOGLE+_URL').'">'.$template->generateIcon('google-plus').'</a>';
        
        $x .= '</div>';
    }
    $x .= '<small>&copy;2013 - ' . date('Y') . ' Dominic Masters.</small>';
    $x .= '</div>';
    $x .= '</div>';
    $x .= '</small>';
    $x .= '</div>';
    $x .= '</div>';
    $x .= '</div>';
    
    $x .= '</div>';
    
    //Cuz I'm such a fuckin' nerd here's the page processing time
    global $imported_classes;
    $x .= "<!--\n";
    $x .= "Page Processing Took: " . (microtime(true)-SCRIPT_LOADTIME);
    $x .= "\nClasses Loaded: " . $imported_classes->size();
    $x .= "\n-->";
    
    $x .= '</body>';
    $x .= '</html>';

        
    return $x;//Finally return the string buffer
}, function() {
    //Construct Code Here
});