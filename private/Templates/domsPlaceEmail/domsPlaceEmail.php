<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Template');
import('Page.Page');
import('Email.Email');

class domsPlaceEmail extends Template {
    public function __construct() {
        parent::__construct('domsPlaceEmail', '1.00', __FILE__, strtotime('29th February 2016'));
    }
    
    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {
        if(!($page instanceof Email)) throw new Exception('Not an email.');
        parent::startPage($page);
    }
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {
        if(!($page instanceof Email)) throw new Exception('Not an email.');
        
        $remove_border_and_padding = ' cellspacing="0" cellpadding="0" border="0" style="border: 0px none; border-collapse: collapse; border-spacing: 0px; margin: 0px; padding: 0px;';
        
        //$stars = Template::getSystemTemplate('domsPlace')->getFolder()->getChild('Images')->getChild('bg.png');
        $stars = File::getTopDirectory()->getChild('images')->getChild('bg.png');
        //$logo = Template::getSystemTemplate('domsPlace')->getFolder()->getChild('Images')->getChild('logo.png');
        $logo = File::getTopDirectory()->getChild('images')->getChild('logo.png');
        
        $bg0 = $remove_border_and_padding . 'background-image:url(\''.getconf('URL').$stars->getAsHTTPPath().'\');background-repeat: repeat;';
        $bg1 = $remove_border_and_padding . 'background:#FFFFFF;';
        
        $remove_border_and_padding .= '"';
        $bg0 .= '"';
        $bg1 .= '"';
        
        $x = '<!DOCTYPE HTML>';
        $x .= '<html lang="en" style="margin: 0px !important; padding: 0px !important">';
        $x .= '<head>';
        $x .= '<title>' . $page->getSubject() . '</title>';
        $x .= '</head>';
        
        $x .= '<body style="margin: 0px !important; padding: 0px !important">';
        
        $x .= '<table width="100%"'.$bg0.'>';//Because fuck outlook.
        $x .= '<tr align="center"'.$remove_border_and_padding.'>';
        $x .= '<td align="center"'.$remove_border_and_padding.'>';{
            
            $x .= '<table width="600"'.$remove_border_and_padding.'>';
            $x .= '<tr'.$remove_border_and_padding.'>';
            $x .= '<td'.$remove_border_and_padding.'>';{
                $x .= '<br /><a href="'.getconf('URL').File::getTopDirectoryAsHTTP().'">';
                $x .= '<img src="'.getconf('URL').$logo->getAsHTTPPath().'" alt="'.getconf('domsPlace').'" />';
                $x .= '</a><br /><br />';
            }
            $x .= '</td>';
            $x .= '</tr>';
            
            $x .= '<tr'.$remove_border_and_padding.'>';
            $x .= '<td'.$bg1.'>';
            $x .= '<div style="font: 14px Arial,Helvetica,sans-serif; text-align: justify; padding: 4px;">';
            $x .= $data;
            $x .= '</div>';
            $x .= '</td>';
            $x .= '</tr>';
            
            $x .= '<tr'.$bg1.'>';
            $x .= '<td'.$remove_border_and_padding.'>';{
                $x .= '<div style="font: 14px Arial,Helvetica,sans-serif; white-space: nowrap;" align="center">';
                $x .= '<br />';
                $x .= '<small><span class="copyright">&copy;</span>2013 - ' . date('Y') . ' Dominic Masters.</small>';
                $x .= '</div>';
            }
            $x .='</td>';
            $x .= '</tr>';
            
            $x .= '<tr'.$remove_border_and_padding.'>';
            $x .= '<td'.$remove_border_and_padding.'>&nbsp;</td>';
            $x .= '</tr>';
            
            $x .= '</table>';
            
        }$x .= '</td>';
        $x .= '</tr>';
        $x .= '</table>';
        
        $x .= '</body>';
        $x .= '</html>';
        
        return $x;
    }
}