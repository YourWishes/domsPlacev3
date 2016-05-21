<?php
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

import('Event.*');
import('Database.VirtualDatabase.VirtualChange.VirtualChange');

/**
 * Description of onVirtualChange
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class onVirtualChange extends Event {
    public static $VIRTUAL_CHANGE_EVENT_TYPE;
   
    //Instance
    private $virtual_change;
    
    public function __construct($virtual_change) {
        if(!($virtual_change instanceof VirtualChange)) throw new Exception('Invalid VirtualChange type.');
        parent::__construct(onVirtualChange::$VIRTUAL_CHANGE_EVENT_TYPE);
        $this->virtual_change = $virtual_change;
    }
}

if(!isset(onVirtualChange::$VIRTUAL_CHANGE_EVENT_TYPE)) onVirtualChange::$VIRTUAL_CHANGE_EVENT_TYPE = new EventType('onVirtualChange', 'onVirtualChange');