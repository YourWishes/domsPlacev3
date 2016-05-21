<?php
namespace UsersAddon;

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

import('Event.Event');
import('Event.EventType');

/**
 * Description of PageRequestEvent
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class UserRegisterEvent extends \Event {
    public static $EVENT_TYPE;
    
    //Instance
    private $user;
    
    public function __construct($user) {
        if(!($user instanceof RegisteredUser)) throw new \Exception("Invalid User.");
        parent::__construct(static::$EVENT_TYPE);
        $this->user = $user;
    }
    
    /**
     * 
     * @return RegisteredUser
     */
    public function getUser() {return $this->user;}
}
UserRegisterEvent::$EVENT_TYPE = new \EventType('User Registered', '\UsersAddon\UserRegisteredEvent');
