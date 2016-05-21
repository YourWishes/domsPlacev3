<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Form', function(&$template, &$page, $data) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception ();
    if(!($data instanceof \FormsAddon\Form)) throw new Exception("Invalid Form.");
    //Generate Code Here
    
    //Now the HTML
    $x = '<form class="form-horizontal" ';
    $x .= 'method="' . $data->getSubmitType()->getName() . '" ';
    
    if($data->getAction() instanceof AjaxRequestHandler) {
        $x .= 'onSubmit="return ajaxForm(this)" data-request="'.$data->getAction()->getRequestListener().'" action="/" ';
    } else {
        $x .= 'action="' . $data->getAction() . '" ';
    }
    
    $x .= '>';
    
    foreach($data->getControls() as $control) {
        if(!($control instanceof \FormsAddon\FormControl)) continue;
        $hidden = $control->getType() === FormsAddon\FormControlType::$HIDDEN;
        $checkbox = $control->getType() === FormsAddon\FormControlType::$CHECKBOX;
        $button = false;
        if(
                $control->getType() == FormsAddon\FormControlType::$BUTTON || 
                $control->getType() == \FormsAddon\FormControlType::$SUBMIT
            ) {
            $button = true;
        }
        
        if(!$hidden) {
            $x .= '<div class="form-group">';

            if($button || $checkbox) {
                $x .= '<div class="col-sm-offset-2 col-sm-10">';
            } else {
                $x .= '<label for="'.$control->getName().'" class="col-sm-2 control-label">'.$control->label.'</label>';
                $x .= '<div class="col-sm-10">';
            }
        }
        
        if($checkbox) $x .= '<div class="checkbox"><label>';
        
        $x .= '<' . $control->getType()->getTag();
        if($control->getName() !== null) $x .= ' name="' . $control->getName() . '"';
        $x .= ' id="inp-' . $control->getName() . '"';
        $x .= ' type="' . $control->getType()->getName() . '"';
        if(!$checkbox) $x .= ' class="form-control"';
        if($control->required) $x .= ' required';
        if(isset($control->placeholder)) $x .= ' placeholder="'.$control->placeholder.'"';
        if(isset($control->onClick)) $x .= ' onClick="'.$control->onClick.'"';
        
        if($control->getType()->closeTag()) {
            $x .= ' >';
            if(isset($control->value)) $x .= $control->value;
            
            if(isset($control->options) && $control->options instanceof \ArrayList) {
                //For Select Tags
                foreach($control->options as $option) {
                    if(!($option instanceof FormsAddon\FormControl)) continue;
                    
                    $x .= '<' . $option->getType()->getTag();
                    if($option->getName() !== null) $x .= ' name="' . $option->getName() . '"';
                    $x .= ' id="inp-' . $option->getName() . '"';
                    $x .= ' type="' . $option->getType()->getName() . '"';
                    if($option->required) $x .= ' required';
                    if(isset($option->placeholder)) $x .= ' placeholder="'.$option->placeholder.'"';
                    if(isset($option->onClick)) $x .= ' onClick="'.$option->onClick.'"';
                    if($option->value == $control->value) $x .= ' selected';
                    if($option->getType()->closeTag()) {
                        if(isset($option->value)) $x .= ' value="' . $option->value. '"';
                        $x .= ' >';
                        if(isset($option->label)) $x .= $option->label;
                        $x .= '</' . $option->getType()->getTag() . '>';
                    } else {
                        if(isset($option->value)) $x .= ' value="' . $option->value . '"';
                        $x .= ' />';
                    }
                }
            }
            
            $x .= '</' . $control->getType()->getTag() . '>';
        } else {
            if(isset($control->value)) $x .= ' value="' . $control->value . '"';
            $x .= ' />';
        }
        
        if($checkbox) $x .= $control->label . '</label></div>';
        
        if(isset($control->description)) {
            $x .= '<p class="help-block">';
            $x .= $control->description;
            $x .= '</p>';
        }
        
        if(!$hidden) {
            $x .= '</div>';
            $x .= '</div>';
        }
    }
    
    $x .= '</form>';

    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
});