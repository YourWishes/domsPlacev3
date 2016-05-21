<?php
namespace FormsAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

/*
 * Copyright 2016 Dominic Masters <dominic@domsplace.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Description of FormControl
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class FormControl {
    /**
     * Performs basic input checking if a string is "checked" or not
     * @param mixed $str
     */
    public static function isDataChecked($str) {
        return $str == "on" || $str == "checked" || $str == "true" || $str == "1" || $str == 1;
    }
    
    public static function getSubmitButton() {
        $x = new FormControl('submitbtn', FormControlType::$SUBMIT);
        $x->value = 'Submit';
        return $x;
    }
    
    //Instance
    private $name;
    private $type;
    
    public $max_length;
    public $required=false;
    public $description=null;
    public $value=null;
    public $placeholder;
    public $label;
    public $onClick;
    public $title;
    
    /**
     * 
     * @param string $name
     * @param \FormsAddon\FormControlType $type
     * @throws \Exception
     */
    public function __construct($name, &$type) {
        if(!($type instanceof \FormsAddon\FormControlType)) throw new \Exception("Invalid FormControlType.");
        $this->name = $name;
        $this->type = $type;
        $this->label = $name;
    }
    
    public function getName() {return $this->name;}
    
    /**
     * 
     * @return FormControlType
     */
    public function getType() {return $this->type;}
}