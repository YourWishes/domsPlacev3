<?php
namespace domsPlaceAddon;

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
 * Description of News
 *
 * @author Dominic Masters <dominic@domsplace.com>
 */
class News extends \VirtualClass implements \JsonSerializable {
    public static function getFields() {
        $x = parent::getFields();
        $x->add(static::getTable()->getFields());
        return $x;
    }
    
    public static function getDateField() {
        return static::getTable()->getField('time');
    }
    
    public static function getByID($id, $includeHidden=false) {
        $post = parent::getByID($id);
        if(!($post instanceof News)) return $post;
        if($includeHidden) return $post;
        if(!$post->isHidden()) return $post;
        return null;
    }
    
    /**
     * @return \VirtualTable
     */
    public static function getTable() {
        return domsPlaceAddon::getNewsTable();
    }
    
    public static function getDefaultPostsPerPage() {return 15;}
    
    public static function getFromPage($page, $perPage=null) {
        if($perPage === null) $perPage = static::getDefaultPostsPerPage();
        
        //Build a nice little query
        $query = new \GetQuery();
        $query->addFields(static::getFields());
        $query->addClause(new \OrderClause(static::getDateField(), false));
        $query->addClause(new \LimitClause($perPage, static::pageNumberToOffset($page, $perPage)));
        $query->addClause(new \WhereClause(static::getTable()->getField('hidden'), \ClauseOperator::$EQUALS, false));
        $results = $query->fetch(static::getTable()->database);
        
        $posts = new \ArrayList('\domsPlaceAddon\News');
        foreach($results as $result) {
            $posts->add(static::getByEntry($result));
        }
        
        return $posts;
    }
    
    public static function pageNumberToOffset($page, $perPage=null) {
        if($perPage === null) $perPage = static::getDefaultPostsPerPage();
        if($page < 1) $page = 1;
        $page = min(static::getTotalPages($perPage), $page);
        $offset = ($page-1) * $perPage;
        return $offset;
    }
    
    public static function getTotalPages($perPage=null) {
        if($perPage === null) $perPage = static::getDefaultPostsPerPage();
        
        //First get count
        $list = new \ArrayList();
        $list->add(new \WhereClause(static::getTable()->getField('hidden'), \ClauseOperator::$EQUALS, false));
        
        $count = static::getCount($list);
        $count /= $perPage;//Total Number of Pages
        $count = ceil($count);//Round up (1 post = 1 Page)
        return $count;
    }
    
    /**
     * 
     * @return \FormsAddon\Form
     */
    public static function getBlankForm() {
        $form = new \FormsAddon\Form();
        
        $title = new \FormsAddon\FormControl('title', \FormsAddon\FormControlType::$TEXT);
        $title->label = "Title";
        $title->max_length = static::getTable()->getField('title')->max_length;
        $title->title = "Title";
        $title->placeholder = "Enter a title.";
        $form->addControl($title);
        
        $body = new \FormsAddon\FormControl('body', \FormsAddon\FormControlType::$TEXTAREA);
        $body->label = "Post";
        $body->title = "Post";
        $body->placeholder = "Enter your post here.";
        $body->description = "Raw HTML will be used on posts.";
        $form->addControl($body);
        
        $post = new \FormsAddon\FormControl('post', \FormsAddon\FormControlType::$SUBMIT);
        $post->value = "Post";
        $form->addControl($post);
        
        $form->setAction(CreateNewsPost::getInstance());
        
        return $form;
    }
    
    /**
     * 
     * @return \FormsAddon\Form
     */
    public static function getForm($post) {
        if(!($post instanceof News)) throw new \Exception("Post invalid");
        $form = new \FormsAddon\Form();
        
        $title = new \FormsAddon\FormControl('title', \FormsAddon\FormControlType::$TEXT);
        $title->label = "Title";
        $title->max_length = static::getTable()->getField('title')->max_length;
        $title->title = "Title";
        $title->placeholder = "Enter a title.";
        $title->value = $post->getTitle();
        $form->addControl($title);
        
        $body = new \FormsAddon\FormControl('body', \FormsAddon\FormControlType::$TEXTAREA);
        $body->label = "Post";
        $body->title = "Post";
        $body->placeholder = "Enter your post here.";
        $body->description = "Raw HTML will be used on posts.";
        $body->value = $post->getBody();
        $form->addControl($body);
        
        $id = new \FormsAddon\FormControl('id', \FormsAddon\FormControlType::$HIDDEN);
        $id->value = $post->getID();
        $form->addControl($id);
        
        $post = new \FormsAddon\FormControl('post', \FormsAddon\FormControlType::$SUBMIT);
        $post->value = "Save";
        $form->addControl($post);
        
        $form->setAction(EditNewsPost::getInstance());
        
        return $form;
    }
    
    //Instance
    private $id;
    private $title;
    private $time;
    private $body;
    private $hidden;
    
    private $posted_log;//The original log created when this post was made.

    public function __construct() {
        parent::__construct();
        $this->hidden = false;
    }
    
    public function getID() {return $this->id;}
    public function getTitle() {return $this->title;}
    public function getTime() {return $this->time;}
    public function getBody() {return $this->body;}
    public function isHidden() {return $this->hidden;}
    
    /**
     * 
     * @return NewsPostedLog
     */
    public function getPostedLog() {
        if(isset($this->posted_log)) return $this->posted_log;
        return $this->posted_log = NewsPostedLog::getPostedLogFromPost($this);
    }
    
    public function setID($id) {$this->id = $id;}
    public function setTitle($title) {$this->title = $title;}
    public function setTime($time) {$this->time = $time;}
    public function setBody($body) {$this->body = $body;}
    
    public function hide() {
        $this->hidden = true;
        $this->update();
    }

    public function getValue($field) {
        if(!($field instanceof \VirtualField)) return null;
        if($field->getName() == 'id') return $this->id;
        if($field->getName() == 'title') return $this->title;
        if($field->getName() == 'time') return $this->time;
        if($field->getName() == 'body') return $this->body;
        if($field->getName() == 'hidden') return $this->hidden;

        return null;
    }

    public function setField($field, $value) {
        if(!($field instanceof \VirtualField)) return false;
        if($field->getName() == 'id') {
            $this->id = intval($value);
            return true;
        } else if($field->getName() == 'title') {
            $this->title = $value;
            return true;
        } else if($field->getName() == 'time') {
            $this->time = $value;
            return true;
        } else if($field->getName() == 'body') {
            $this->body = $value;
            return true;
        } else if($field->getName() == 'hidden') {
            $this->hidden = $value;
            return true;
        }

        return false;
    }
    
    /**
     * Similar to the create() function, however fires all needed events as well
     * as logs.
     */
    public function postNews($poster) {
        if(!($poster instanceof \UsersAddon\User)) throw new \Exception ("Invalid User.");
        if(!isset($this->time)) $this->time = new \DateTime();
        
        $ventry = $this->createEntry();
        $db = static::getTable()->database;
        $db->commitChanges();
        $this->id = intval($ventry->getData()->getComp(static::getIDField()));
        
        $this->posted_log = NewsPostedLog::createForPost($poster, $this);
    }
    
    public function canCurrentUserDelete() {
        $user = \UsersAddon\User::getLoggedInUser();
        if(!($user instanceof \UsersAddon\User)) return false;
        return $this->canUserDelete($user);
    }
    
    public function canUserDelete($user) {
        if(!($user instanceof \UsersAddon\User)) return false;
        if(Permissions::DELETE_NEWS_POST()->canUserPerform($user)) return true;
        $posted = $this->getPostedLog();
        if(!($posted instanceof NewsPostedLog)) return false;
        $poster = $posted->getPoster();
        if(!($poster instanceof \UsersAddon\User) || !$poster->compare($user)) return false;
        if(!Permissions::DELETE_OWN_NEWS_POST()->canUserPerform($user)) return false;
        return true;
    }
    
    public function canCurrentUserEdit() {
        $user = \UsersAddon\User::getLoggedInUser();
        if(!($user instanceof \UsersAddon\User)) return false;
        return $this->canUserEdit($user);
    }
    
    public function canUserEdit($user) {
        if(!($user instanceof \UsersAddon\User)) return false;
        if(Permissions::EDIT_NEWS_POST()->canUserPerform($user)) return true;
        $posted = $this->getPostedLog();
        if(!($posted instanceof NewsPostedLog)) return false;
        $poster = $posted->getPoster();
        if(!($poster instanceof \UsersAddon\User) || !$poster->compare($user)) return false;
        if(!Permissions::EDIT_OWN_NEWS_POST()->canUserPerform($user)) return false;
        return true;
    }

    public function jsonSerialize() {
        return array(
            "id" => $this->id,
            "title" => $this->title,
            "time" => $this->time,
            "body" => $this->body,
            "hidden" => $this->hidden
        );
    }
}
