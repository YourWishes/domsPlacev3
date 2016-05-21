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

/**
 * Description of ClauseOperator
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class ClauseOperator {
    //Statics
    public static $EQUALS;
    public static $LESS_THAN;
    public static $LESS_THAN_OR_EQUAL_TO;
    public static $GREATER_THAN;
    public static $GREATER_THAN_OR_EQUAL_TO;
    public static $NOT_EQUALS;
    
    //Instance
    private $name;
    private $operator;
    
    public function __construct($name, $operator) {
        $this->name = $name;
        $this->operator = $operator;
    }
    
    public function getName() {return $this->name;}
    public function getOperator() {return $this->operator;}
}

ClauseOperator::$EQUALS = new ClauseOperator('Equals', '=');
ClauseOperator::$LESS_THAN = new ClauseOperator('Less Than', '<');
ClauseOperator::$GREATER_THAN = new ClauseOperator('Greater Than', '>');
ClauseOperator::$LESS_THAN_OR_EQUAL_TO = new ClauseOperator('Less Than Or Equal To', '<=');
ClauseOperator::$GREATER_THAN_OR_EQUAL_TO = new ClauseOperator('Greater Than Or Equal To', '>=');
ClauseOperator::$NOT_EQUALS = new ClauseOperator('Not Equals', '<>');