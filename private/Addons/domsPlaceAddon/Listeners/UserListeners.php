<?php
namespace domsPlaceAddon;
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

import('Event.Event');
import('Event.EventListener');
import('Event.EventType');

/**
 * Description of PageListeners
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class UserListeners extends \EventListener {
    public function __construct() {
        parent::__construct(\UsersAddon\UserRegisterEvent::$EVENT_TYPE);
        $this->register();
    }
    
    public function onEvent(&$event) {
        if(!($event instanceof \UsersAddon\UserRegisterEvent)) return;
        $eml = new \Email();
        $usr = $event->getUser();
        $eml->addRecipient($usr->getEmail());
        $eml->setSubject('Registration Complete!');
        $eml->echoData('<h1>Hello, ' . $eml->getTemplate()->escapeHTML($usr->getUsername()) . '</h1>');
        $eml->echoData('<p>Thank you for joining my website! Please don\'t reply to this automatic email.</p>');
        $eml->echoData('<p>If you feel this is a mistake, feel free to contact me by clicking <a href="'.getconf('URL').\File::getTopDirectoryAsHTTP().'">Here</a></p>');
        $eml->send();
    }
}

