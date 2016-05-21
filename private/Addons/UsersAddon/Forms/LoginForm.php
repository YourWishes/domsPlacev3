<?php
namespace UsersAddon;
if (!defined('MAIN_INCLUDED')) throw new \Exception();

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
 * Description of User
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class LoginForm extends \FormsAddon\Form {
    public $username;
    public $password;
    public $submit;
    
    public function __construct() {
        parent::__construct();
        
        $this->setSubmitType(\FormsAddon\FormSubmitType::$POST);
        
        $this->username = new \FormsAddon\FormControl('username', \FormsAddon\FormControlType::$TEXT);
        $this->password = new \FormsAddon\FormControl('password', \FormsAddon\FormControlType::$PASSWORD);
        $this->submit = \FormsAddon\FormControl::getSubmitButton();
        
        $this->username->max_length = User::getUsersAddon()->getRegisteredUsersTable()->getField('name')->max_length;
        $this->password->max_length = User::getUsersAddon()->getPasswordsTable()->getField('password')->max_length;
        
        $this->username->required = true;
        $this->password->required = true;
        
        $this->username->label = "Username";
        $this->password->label = "Password";
        
        $this->submit->value = 'Login';
        
        $this->addControl($this->username);
        $this->addControl($this->password);
        $this->addControl($this->submit);
    }
}
