<?php
/*
 * Copyright 2016 Dominic Masters.
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

if(!class_exists('Comparable')) {
    interface Comparable {
        public function compare(Comparable $to);
    }
}

/**
 * ArrayList v1.00
 * Java/C# like ArrayLists for PHP.
 * 
 * @author Dominic Masters <dominic@domsplace.com>
 */
class ArrayList implements Iterator, ArrayAccess, JsonSerializable, Comparable {
    //Static Methods
    
    /**
     * Splits a string, an array or an ArrayList by one of 4 different methods.
     *      ArrayList (Of any of the 4 object types)
     *      Array (Of any of the 4 object types)
     *      String (To explode by)
     *      Regular Expression (To split by)
     * 
     * This is a powerful tool, but a lot of recursion will occur if using an
     * array and/or an ArrayList.
     * 
     * And 4 basic use cases:
     *      Split a String by a String
     *      Split a String by an Array (nested?) of Strings
     *      Split an Array (nested?) of Strings by a String
     *      Split an Array (nested?) of Strings by an Array (nested?) of Strings
     * 
     * Using Arrays as one of the two parameters can be a tad messy, but easy
     * once you figure it out.
     * 
     * No Matter what an array is returned for each delimiter string and each
     * "split" string, no matter how nested. Using multiple delimiters will not
     * cause the string to be split by multiple delimiters, but instead to split
     * each delimiter into another set of split strings (Refer to line 43).
     * 
     * @param ArrayList|string|array $delimiters The character(s) you're splitting by, can be inside arrays/nested arrays.
     * @param string $string The string you are trying to split.
     * @param bool $allowDuplicates If true then the normal ArrayList "remove duplicate objects" will be ignored, and instead the same value/object may appear in the ArrayList twice.
     * @return ArrayList Split String as an ArrayList, may contain sub-arraylists.
     * @throws Exception
     */
    public static function explode($delimiters, &$string, $allowDuplicates=true) {
        if(!is_string($string) && !is_array($string) && !($string instanceof ArrayList)) throw new Exception('String is invalid.');
        //$delimiters may be a String (1 delimiter), an ArrayList, an Array or a Regular Expression
        
        
        //Array/ArrayList Delimiters
        if($delimiters instanceof ArrayList) {
            $x = new ArrayList();
            foreach($delimiters as $delimiter) {
                $x->add($string = ArrayList::explode($delimiter, $string), false, true);
            }
            return $x;
        } else if(is_array($delimiters)) {
            return ArrayList::explode(new ArrayList($delimiters), $string);//CHEAP
        } else if(is_string($delimiters)) {
            if(is_string($string)) {
                return new ArrayList(explode($delimiters, $string));
            } else if(is_array($string)) {
                return ArrayList::explode($delimiters, new ArrayList($string));
            } else if($string instanceof ArrayList) {
                $x = new ArrayList();
                foreach($string as $str) {
                    $x->add(ArrayList::explode($delimiters, $str), false, true);
                }
                return $x;
            }
        } else if(@preg_match($delimiters, null) !== false){
            //return new ArrayList(preg_split($delimiters, $string));
        }
        
        throw new Exception('Invalid Delimiters (Must be Array, ArrayList, Regular Expression or String)');
    }
    
    //Instance
    private $internal_array;
    private $classNames;
    
    private $position;
    
    /**
     * Construct an ArrayList. ArrayLists are simply managed arrays that are 
     * confined to certain types, as well as offering key and value lookups.
     * 
     * Class name can be set as either one string of a class name, or multiple
     * accepted strings seperated by Pipe-Lines (|).
     * 
     * @param className|ArrayList|array $className
     */
    public function __construct($className=null,$optional0=null) {
        $this->position = 0;
        if($className instanceof ArrayList) {
            $this->internal_array = $className->internal_array;
            if(isset($className->className)) {
                $this->classNames = $className->classNames;
            }
        } else if(is_array($className)) {
            $this->internal_array = $className;//YAY
        } else {
            $this->internal_array = array();//Construct the array that the list is.
            if(is_array($optional0)) {
                $this->internal_array = $optional0;
            }
            if($className !== null) {
                $cnames = explode('|', $className);
                foreach($cnames as $cname) {
                    if(!class_exists($cname)) throw new Exception('Invalid Class "' . $cname . '"');
                }
                $this->classNames = $cnames;
            }
        }
    }
    
    /**
     * Searches the allowed class names in this array list for the supplied 
     * class name. If valid it will return true, otherwise false.
     * 
     * @param className $className
     * @return boolean
     */
    public function isValidClass($className) {
        if(!isset($this->classNames)) return true;
        foreach($this->classNames as $name) {
            if(is_subclass_of($name, $className)) return true;
            if($name === $className) return true;
        }
        return false;
    }
    
    /**
     * Similar to the isValidClass function, this function will return true if
     * the supplied object is an instanceof one of the allowed classes by this
     * ArrayList, otherwise it will return false.
     * 
     * @param mixed $object
     * @return boolean
     */
    public function isValidInstance($object) {
        if(!isset($this->classNames)) return true;
        foreach($this->classNames as $name) {
            if(!($object instanceof $name)) continue;
            return true;
        }
        return false;
    }
    
    /**
     * Returns the value at the specified index.
     * 
     * @param string|int $index
     * @return type
     * @throws Exception
     */
    public function &get($index) {
        if(!is_int($index) && !is_string($index)) throw new Exception('Index must be an integer or string.');
        if(!isset($this->internal_array[$index]) && !is_null($this->internal_array[$index])) throw new Exception ('Index undefined');
        return $this->internal_array[$index];
    }
    
    /**
     * Sets the value into the array.
     * 
     * @param string|int $index
     * @param mixed $value
     * @throws Exception
     */
    public function set($index, &$value) {
        if(!is_int($index) && !is_string($index)) throw new Exception('Index must be an integer or string.');
        if(!$this->isValidInstance($value)) {
            throw new Exception('Invalid Type.');
        }
        
        $this->internal_array[$index] = $value;
    }
    
    /**
     * Pushes the specified object to the Array, or returns false if the object
     * is already in the array.
     * 
     * You can also pass another ArrayList to merge them together. Keys will be
     * lost however, please note. Returns an integer containing the amount of
     * elements added.
     * 
     * @param mixed $obj
     * @param bool $mergeLists If true any ArrayList passed will be merged, if false then a nested ArrayList will be added.
     * @param bool $allowDuplicates If true then duplicate entries may be added to the database.
     * @throws Exception Throws Exception if the object type is wrong
     * @return bool|int
     */
    public function add($obj, $mergeLists = true, $allowDuplicates=false) {
        if($obj instanceof ArrayList && $mergeLists) {
            /*
             * Leftover code from the time when the ArrayList only supported the
             * one type:
             * 
             * if(isset($this->className) && isset($obj->className) && $obj->className !== $this->className) {
             *      throw new Exception('ArrayList has an invalid type.');
             * }
             * 
             * My Plan originally was to just enforce no similar types, but I 
             * thought that maybe I'd enforce a merge-like attitude, since it's 
             * both more efficient and easier to implement.
             * 
             * Basically if you have two ArrayLists, like so:
             * 
             * $arrList1 = new ArrayList('ObjectA|ObjectB');
             * $arrList2 = new ArrayList('ObjectB|ObjectC');
             * 
             * $arrList1->add(new ObjectA(0));
             * $arrList1->add(new ObjectB(1));
             * $arrList2->add(new ObjectB(2));
             * $arrList2->add(new ObjectC(3));
             * 
             * $arrList2->add($arrList1);
             * 
             * Will result in $arrList2 containing:
             * [
             *  0 => ObjectB(){id: 2},
             *  1 => ObjectC(){id: 3},
             *  2 => ObjectB(){id: 1}
             * ];
             * 
             * But not ObjectA
             */
            
            /* Due to the nature of PHP there is only one proper way to enforce
             * that no one object appears in the array twice.
             * 
             * e.g. if I made the following:
             * 
             * $objectA = new Object('whatever');
             * $objectB = new Object('something');
             * 
             * $array = new ArrayList();
             * $array->add($objectA);
             * $array->add($objectB);
             * 
             * $array2 = new ArrayList();
             * $array2->add($objectA);
             * $array2->add($array);
             * 
             * Using PHP's array_merge, or doing an array concatenation would
             * result in $objectA being in the list twice...
             * 
             * Basically I've ended up with the following, while it's not the
             * most efficient thing in the world it does what I want...
             * 
             * I am still thinking of ways to improve performance in anyway
             * possible.
             */
            
            $itemsAdded = 0;
            foreach($obj as $objectInOtherList) {
                try {
                    $result = $this->add($objectInOtherList, $mergeLists, $allowDuplicates);//This will also help error-check
                } catch(Exception $e) {
                    continue;
                }
                if($result) $itemsAdded++;
            }
            return $itemsAdded;
        } else {
            if(!$this->isValidInstance($obj)) {
                throw new Exception('Invalid Type. (' . get_class($obj) . ")");
            }
            
            if(!$allowDuplicates && $this->contains($obj)) {
                return false;
            }
            array_push($this->internal_array, $obj);
        }
        return true;
    }
    
    /**
     * Removes the object from the array, returns true if removed and false if
     * not removed.
     * 
     * @param mixed $obj
     * @return boolean
     * @throws Exception
     */
    public function remove(&$obj) {
        if($obj instanceof ArrayList) {
            foreach($obj as $k) {
                $this->remove($k);
            }
            return true;
        }
        
        if(!$this->isValidInstance($obj)) {
            throw new Exception('Invalid Type.');
        }
        if(($key = array_search($obj, $this->internal_array)) !== false) {
            $this->removeByIndex($key);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Removes an object from the internal array by its array key
     * 
     * @param int|string $key
     */
    public function removeByIndex($key) {
        if(is_int($key)) {
            array_splice($this->internal_array, $key, 1);
        } else {
            unset($this->internal_array[$key]);
        }
    }
    
    /**
     * Returns true if the object is in the array, otherwise returns false.
     * 
     * @param mixed $obj
     * @return bool
     */
    public function contains($obj,$strict=false) {
        $in_arr = in_array($obj, $this->internal_array, $strict);
        if($in_arr === true) {
            return true;
        }
        if(!($obj instanceof Comparable)) {
            return false;
        }
        
        //Iterate and compare...
        foreach($this->internal_array as $key => $value) {
            $r = $obj->compare($value);
            if(!($r)) continue;
            return true;
        }
        return false;
    }
    
    /**
     * Returns the size of the array (Amount of elements in the list)
     * 
     * @return int
     */
    public function size() {
        return sizeof($this->internal_array);
    }
    
    /**
     * Creates a clone of the internal ArrayObject
     * 
     * @return array
     */
    public function getAll() {
        return $this->internal_array;
    }
    
    /**
     * 
     * @param string|int $key
     * @return bool
     * @throws Exception
     */
    public function isKeySet($key) {
        if(!isset($this->internal_array[$key])) return false;
        return true;
    }
    
    /**
     * Returns true if the ArrayList contains no items, false if it does.
     * 
     * @return bool
     */
    public function isEmpty() {
        if($this->size() == 0) return true;
        return false;
    }
    
    /**
     * Filters this list by the function in the class, and the required result.
     * Function arguments can be supplied as an array. This function uses the 
     * built-in PHP function call_user_func_array.
     * 
     * Setting $strict to true will force === comparison.
     * 
     * Performance for this function is lack-luster at best.
     * 
     * @param callable $function
     * @param mixed $required_result
     * @param array $arguments
     * @param bool $strict
     * @return ArrayList
     */
    public function filter($function, $required_result, $arguments=array(), $strict=false) {
        foreach($this->createCopy() as $k => $value) {
            $result = call_user_func_array(array($value, $function), $arguments);
            if($strict && $result !== $required_result) {
                $this->remove($value);
            } else if($result != $required_result) {
                $this->remove($value);
            }
        }
        return $this;
    }
    
    /**
     * 
     * @param type $clazz
     * @param type $allow_superclasses
     * @return \ArrayList
     */
    public function filterByType($clazz, $allow_superclasses=true) {
        foreach($this->createCopy() as $value) {
            $r = get_class($value);
            if($r == $clazz) continue;
            $this->remove($value);
        }
        return $this;
    }
    
    /**
     * Returns an element in the ArrayList, based on the result of running one
     * of it's functions. This function uses the built-in PHP function 
     * call_user_func_array.
     * 
     * Setting $strict to true will force === comparison.
     * 
     * @param callable $function
     * @param mixed $required_result
     * @param array $arguments
     * @param bool $strict
     * @return mixed
     */
    public function getByFunctionValue($function, $required_result, $arguments=array(), $strict=false) {
        foreach($this->internal_array as $key => $value) {
            $result = call_user_func_array(array($value, $function), $arguments);
            if($strict && $result !== $required_result) {
                continue;
            } else if($result != $required_result) {
                continue;
            }
            return $value;
        }
        return null;
    }
    
    public function sort() {
        return usort($this->internal_array);
    }
    
    public function reverse() {
        $this->internal_array = array_reverse($this->internal_array);
        return $this;
    }
    
    
    private $sort_cmp_func_function;
    private $sort_cmp_func_arguments;
    private $sort_cmp_func_strnatcmp;
    
    /**
     * Sorts the internal ArrayList by a supplied function (and optionally any
     * supplied arguments). Set $strnatcmp to true to use string human 
     * comparison. Alternatively you can override the sort_cmp_func function.
     * I will be making this easier to override shortly.
     * 
     * @param type $function
     * @param type $arguments
     * @param type $strnatcmp
     * @return \ArrayList
     */
    public function sortByFunctionValue($function, $arguments=array(), $strnatcmp=false) {
        $this->sort_cmp_func_function = $function;
        $this->sort_cmp_func_arguments = $arguments;
        $this->sort_cmp_func_strnatcmp = $strnatcmp;
        
        usort($this->internal_array, array($this, "sort_cmp_func"));
        return $this;
    }
    
    //Internal function for the last compare.
    public function sort_cmp_func($a, $b) {
        if(is_array($this->sort_cmp_func_function)) {//Supplied function is an array... we will need to nest the call functions
            if(!function_exists('recursive_sort_cmp_func')) {
                function recursive_sort_cmp_func($obj, $function_array, $arguments_array) {
                    if(sizeof($function_array) > 1){
                        $func = array_shift($function_array);
                        $res = call_user_func_array(array($obj, $func), $arguments_array);
                        return recursive_sort_cmp_func($res, $function_array, $arguments_array);
                    }
                    
                    $res = call_user_func_array(array($obj, $function_array[0]), $arguments_array);
                    return $res;
                }
            }
            
            $resulta = recursive_sort_cmp_func($a, $this->sort_cmp_func_function, $this->sort_cmp_func_arguments, $this->sort_cmp_func_strnatcmp);
            $resultb = recursive_sort_cmp_func($b, $this->sort_cmp_func_function, $this->sort_cmp_func_arguments, $this->sort_cmp_func_strnatcmp);
        } else {
            $resulta = call_user_func_array(array($a, $this->sort_cmp_func_function), $this->sort_cmp_func_arguments);
            $resultb = call_user_func_array(array($b, $this->sort_cmp_func_function), $this->sort_cmp_func_arguments);
        }
        
        $res = 0;
        if($this->sort_cmp_func_strnatcmp) {
            $res = strnatcmp($resulta, $resultb);
        } else {
            $floatvala = floatval($resulta);
            $floatvalb = floatval($resultb);
            $res = ($floatvala < $floatvalb) ? -1 : 1;
        }
        
        return $res;
    }
    
    /**
     * Clones this object, copying values and indexes of array, as well as the
     * class restriction information.
     * 
     * Clone of simply running 
     * new ArrayList($x);
     * Where $x instanceof ArrayList
     * 
     * @return ArrayList
     */
    public function createCopy() {
        return new ArrayList($this);
    }
    
    /**
     * Same as PHP native function. This will not work on objects that aren't
     * implicitly string castable.
     * 
     * @param string $delimiter
     */
    public function implode($delimiter=', ') {
        return implode($delimiter, $this->internal_array);
    }
    
    public function implode_by_function($function,$delimiter='', $arguments=array()) {
        $x = '';
        $c = 0;
        foreach($this->internal_array as $value) {
            $result = call_user_func_array(array($value, $function), $arguments);
            $x .= $result;
            if(++$c < sizeof($this->internal_array)) $x .= $delimiter;
        }
        return $x;
    }
    
    function rewind() {
        $this->position = 0;
    }
    
    function current() {
        return $this->internal_array[$this->position];
    }
    
    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }
    
    function valid() {
        return isset($this->internal_array[$this->position]);
    }

    public function offsetExists($offset) {
        return $this->isKeySet($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset) {
        $this->removeByIndex($offset);
    }
    
    public function jsonSerialize() {
        return $this->internal_array;
    }
    
    public function __toString() {
        return json_encode($this);
    }

    public function compare(\Comparable $to) {
        if(!($to instanceof ArrayList)) return false;
        if(!($to->size() == $this->size())) return false;
        if($this->internal_array == $to->internal_array) return true;
        //TODO: Maybe iterate over values and compare? needs a more efficient way to do this.
        return false;
    }
}
