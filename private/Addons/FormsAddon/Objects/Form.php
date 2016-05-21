<?php

namespace FormsAddon;

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

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
 * Description of Form
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class Form {

    private $controls;
    private $submit_type;
    private $action;

    public function __construct() {
        $this->submit_type = FormSubmitType::$GET;
        $this->action = '/';
        $this->controls = new \ArrayList('\FormsAddon\FormControl');
    }

    /**
     * 
     * @return ArrayList
     */
    public function getControls() {
        return $this->controls->createCopy();
    }
    
    /**
     * 
     * @return string|AjaxRequestHandler
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * 
     * @return FormSubmitType
     */
    public function getSubmitType() {
        return $this->submit_type;
    }

    public function setAction($action) {
        if (!(is_string($action)) && !($action instanceof \AjaxRequestHandler)) {
            throw new \Exception("Invalid ActionType");
        }
        $this->action = $action;
    }

    public function setSubmitType($submit_type) {
        if (!($submit_type instanceof FormSubmitType))
            throw new \Exception("Invalid Submit Type");
        $this->submit_type = $submit_type;
        return $this;
    }

    /**
     * 
     * @param \FormsAddon\FormControl $control
     * @return \FormsAddon\Form
     * @throws \Exception
     */
    public function addControl($control) {
        if (!($control instanceof FormControl))
            throw new \Exception("Invalid Control Type");
        $this->controls->add($control);
        return $this;
    }

    public function removeControl($control) {
        $this->controls->remove($control);
    }
}
