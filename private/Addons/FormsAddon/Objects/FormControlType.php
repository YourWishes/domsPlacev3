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
class FormControlType {
    public static $TEXT;
    public static $TEXTAREA;
    public static $PASSWORD;
    public static $EMAIL;
    public static $CHECKBOX;
    public static $SUBMIT;
    public static $BUTTON;
    public static $HIDDEN;
    public static $SELECT;
    public static $OPTION;
    
    //Instance
    private $name;
    private $tag;
    private $closetag;
    
    public function __construct($name, $tag='input', $closetag=false) {
        $this->name = $name;
        $this->tag = $tag;
        $this->closetag = $closetag;
    }
    
    public function getName() {return $this->name;}
    public function getTag() {return $this->tag;}
    public function closeTag() {return $this->closetag;}
}

FormControlType::$TEXT = new FormControlType('text');
FormControlType::$TEXTAREA = new FormControlType('textarea', 'textarea', true);
FormControlType::$PASSWORD = new FormControlType('password');
FormControlType::$EMAIL = new FormControlType('email');
FormControlType::$CHECKBOX = new FormControlType('checkbox');
FormControlType::$SUBMIT = new FormControlType('submit', 'button', true);
FormControlType::$BUTTON = new FormControlType('button', 'button', true);
FormControlType::$HIDDEN = new FormControlType('hidden');
FormControlType::$SELECT = new FormControlType('select', 'select', true);
FormControlType::$OPTION = new FormControlType('option', 'option', true);