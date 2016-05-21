<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();
import('Ajax.AjaxRequestEvent');

import('Page.Page');
import('Page.HTTPResponse');

class AjaxRequest extends Event {
    /**
     * Thanks to Svish for the regex validator for JSONP callback requests.
     * To enhance security we need this function in order to validate.
     * 
     * http://www.geekality.net/2010/06/27/php-how-to-easily-provide-json-and-jsonp/
     * 
     * @param string $subject
     * @return type
     */
    public static function isValidJSONPCallback($subject) {
        $identifier_syntax
          = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

        $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
          'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
          'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
          'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
          'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
          'private', 'public', 'yield', 'interface', 'package', 'protected',
          'static', 'null', 'true', 'false');

        return preg_match($identifier_syntax, $subject)
            && ! in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
    }
    
    //Instance
    private $request;
    private $data;
    private $jsonp;
    private $jsonp_callback;
    
    public function __construct($request, $data, $jsonp=false, $jsonp_callback=null) {
        if(!is_array($data)) throw new Exception('Data must be an array');
        parent::__construct(AjaxRequestEvent::$AJAX_REQUEST_EVENT);
        
        $this->request = $request;
        $this->data = $data;
        $this->jsonp = $jsonp;
        $this->jsonp_callback = $jsonp_callback;
    }
    
    public function getRequest() {return $this->request;}
    /**
     * 
     * @return array
     */
    public function getData() {return $this->data;}
    public function isJSONP() {return $this->jsonp;}
    public function getJSONPCallback() {return $this->jsonp_callback;}
    
    /**
     * Sends a passed object of any json_encode'able type to the client, also
     * ends the event processing and finishes the page.
     * 
     * Things to note:
     *      This uses JSON, only.
     *      This will allow JSONP (unless disabled in the config)
     *      The System Template "AJAXTemplate" will be used, and MUST be
     *          available.
     * 
     * @param Object $object
     * @throws Exception
     */
    public function send($object) {
        //Sends the variable $object to the client as a properly formatted JSON object.
        $page = Page::getNewPage();
        
        if($this->jsonp) {
            if(!getconf('ALLOW_JSONP')) throw new Exception('JSONP is not allowed!');
            $page->setContentType(ContentType::$APPLICATION_JAVASCRIPT);
        } else {
            $page->setContentType(ContentType::$APPLICATION_JSON);
        }
        
        //Import the Ajax Template if possible
        try {
            Template::importTemplateClass('AJAXTemplate');
        } catch(Exception $e) {
            throw new Exception('Failed to import AJAX Template, please ensure it exists!', 0, $e);
        }
        
        //Now create the template
        $template_ajax = new AJAXTemplate($this);
        $page->setTemplate($template_ajax);
        
        $page->startPage();
        $page->setData($object);
        $page->endPage();
    }
    
    public function sendError($object) {
        //Sends the variable $object to the client as a properly formatted JSON object.
        $page = Page::getNewPage();
        $page->setResponse(HTTPResponse::$BAD_REQUEST_400);
        $page->setContentType(ContentType::$APPLICATION_JSON);
        
        $page->startPage();
        $page->echoData('Error: ' . $object->getMessage());
        //$page->setData($object);
        $page->endPage();
    }
}