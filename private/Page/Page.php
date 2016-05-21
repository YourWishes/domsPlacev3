<?php

if (!defined('MAIN_INCLUDED'))
    throw new Exception();

import('CharacterSet.CharacterSet');
import('ContentType.ContentType');
import('Language.Language');
import('Page.HTTPResponse');
import('Template.Template');
import('Database.ManagedConnection');

/**
 * Page contains the information that is to be printed to the client, page data
 * can be stored and recalled throughout teh PHP process.
 * 
 * Multiple Page instances can be created, but ultimately only one can be sent 
 * to the client.
 *
 * @author Dominic
 */
class Page {

    private static $activePage = null; //Used for most pages as a simple holder.

    /**
     * getPage
     * 
     * Returns the current page (or makes a new page instance if none exists).
     * Used for pages that only create one instance.
     * 
     * @param string $asking
     * @return Page
     */

    public static function getPage($askingFile = null) {
        if (Page::$activePage !== null && Page::$activePage instanceof Page)
            return Page::$activePage;
        return Page::getNewPage($askingFile)->setAsActivePage();
    }

    public static function hasPageBeenCreated() {
        return Page::$activePage !== null && Page::$activePage instanceof Page;
    }

    /**
     * Create a brand new Page, page will not be marked as active however.
     * @param string $askingFile
     * @return Page
     */
    public static function getNewPage($askingFile = null) {
        $page = new Page($askingFile);
        return $page;
    }

    /**
     * Starts a raw code read, you need to be aware that you must end, and clear
     * the output buffer before you can start any more processing.
     * 
     * Other things to consider:
     *  Tabs, spaces, etc. will not be 'minified' (by default) for any raw
     *      processing done.
     * 
     *  Character sets may be wrong... this is especially a problem when cross
     *      Operating System coding.
     * 
     *  Security may come into play, I suggest cleaning the output buffer before
     *      starting a new raw read incase anyone else has written to the buffer
     *      without cleaning it first.
     * 
     * @param bool $clean
     */
    public static function startRaw($clean = true) {
        if ($clean)
            Page::cleanRaw();
        ob_start();
    }

    /**
     * End the output buffer, if $clean is true then the buffer will be cleaned.
     * If false it will be flushed to the client.
     * 
     * @param bool $clean
     */
    public static function endRaw($clean = true) {
        if ($clean) {
            ob_end_clean();
        } else {
            ob_end_flush();
        }
    }

    public static function rawGet() {
        return ob_get_contents();
    }

    public static function endRawGet($clean = true) {
        $x = Page::rawGet();
        Page::endRaw($clean);
        return $x;
    }

    public static function cleanRaw() {
        if(!ob_get_length()) return;
        ob_clean();
    }

    //Now for static pages... essentially pages that can be accessed staticly.

    /**
     * Returns the HomePage file.
     * 
     * @return File
     */
    public static function getHomePage() {
        //Home Pages are USUALLY referred to as index.[extension]
        $f = File::getMainDirectory();
        $f = $f->getParent();
        $f = new File('index' . SCRIPT_EXTENSION, $f);
        return $f;
    }

    public static function debug($something) {
        $page = Page::getNewPage();
        $page->startPage();
        $page->echoData(json_encode($something));
        $page->makePage();
        $page->endPage();
    }
    
    /**
     * Added 2016/03/02 when switching from the "/System/" code style to the new
     * "/private/" style. Allows a template to get the raw Javascript in the
     * /private/Scripts/Main.js file
     */
    public static function getMainJavascript() {
        $x = 'var DOC_ROOT = "'.File::getTopDirectoryAsHTTP().'";';
        $x .= \File::getMainDirectory()->getChild('Scripts')->getChild('Main.js')->getFileContents();
        return $x;
    }

    //Instance
    private $file;
    private $title;
    private $titlePrefix;
    private $titlePostfix;
    private $date;
    public $cachePage;
    private $charset;
    private $contentType;
    private $template;
    private $language;
    private $response;
    private $tags;
    private $data;  //The Generated HTML to ultimately get sent to the client.

    public function __construct($askingFile) {
        if ($askingFile == null) {
            $askingFile = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
        }

        $this->file = $askingFile;
        $this->title = 'Untitled';
        $this->titlePrefix = getconf('TITLE_PREFIX');
        $this->titlePostfix = getconf('TITLE_POSTFIX');
        $this->date = time();
        $this->cachePage = false;
        $this->charset = CharacterSet::getDefault();
        $this->contentType = ContentType::getDefault();
        $this->template = Template::getDefault();
        $this->language = Language::getDefault();
        $this->response = HTTPResponse::getDefault();

        $this->tags = new ArrayList();
        $this->data = '';
    }

    public function __call($method, $arguments) {
        if (isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $arguments);
        } else {
            throw new Exception("Fatal error: Call to undefined method Page::{$method}()");
        }
    }

    ///Get Methods
    public function getTitle() {
        return $this->title;
    }

    public function getTitlePostfix() {
        return $this->titlePostfix;
    }

    public function getTitlePrefix() {
        return $this->titlePrefix;
    }

    public function getFile() {
        return $this->file;
    }

    public function getDate() {
        return $this->date;
    }

    public function getData() {
        return $this->data;
    }

    /**
     * 
     * @return CharacterSet
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * 
     * @return Template
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * 
     * @return ContentType
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * 
     * @return Language
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * 
     * @return HTTPResponse
     */
    public function getResponse() {
        return $this->response;
    }
    
    /**
     * 
     * @return ArrayList
     */
    public function getTags() {
        return $this->tags;
    }

    ///Set Methods
    public function setTitle($title) {
        $this->title = $title;
    }

    public function setTitlePrefix($prefix) {
        $this->titlePrefix = $prefix;
    }

    public function setTitlePostfix($postfix) {
        $this->titlePostfix = $postfix;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setData($data) {
        $this->data = $data;
    }

    /**
     * 
     * @param CharacterSet $charset
     * @throws Exception
     */
    public function setCharset($charset) {
        if (!($charset instanceof CharacterSet))
            throw new Exception('CharacterSet Type Invalid');
        $this->charset = $charset;
    }

    /**
     * 
     * @param Template $template
     */
    public function setTemplate(&$template) {
        if (!($template instanceof Template))
            throw new Exception('Template Type invalid.');
        $this->template = $template;
    }

    /**
     * 
     * @param ContentType $type
     */
    public function setContentType(&$type) {
        if (!($type instanceof ContentType))
            throw new Exception('Content Type invalid.');
        $this->contentType = $type;
    }

    /**
     * 
     * @param Language $language
     */
    public function setLanguage(&$language) {
        if (!($language instanceof Language))
            throw new Exception('Language Type invalid.');
        $this->language = $language;
    }

    /**
     * 
     * @param HTTPResponse $response
     */
    public function setResponse(&$response) {
        if (!($response instanceof HTTPResponse))
            throw new Exception('Resposne invalid.');
        $this->response = $response;
    }

    /**
     * 
     * @return Page
     */
    public function setAsActivePage() {
        Page::$activePage = $this;
        return $this;
    }

    ///Functions
    /**
     * Why did it take me 6 months to make this function return $this?
     * 
     * @param string $data
     * @return Page
     */
    public function echoData($data) {
        if (!is_string($data)) {
            return $this->echoData(json_encode($data, JSON_PRETTY_PRINT));
        }
        $this->data .= $data;
        return $this;
    }

    /**
     * 
     * @return Page
     */
    public function newLine() {
        $this->echoData($this->template->generateNewLine());
        return $this;
    }

    /**
     * 
     * @return Page
     */
    public function tab() {
        $this->echoData($this->template->generateTab());
        return $this;
    }

    public function escapeHTML($raw) {
        $x = $this->template->escapeHTML($raw);
        return $x;
    }
    
    public function startPage() {
        $this->getTemplate()->startPage($this);
    }

    //Peform standard Page functions (Can be overriden)

    public function makePage() {
        return $this->getTemplate()->make($this->data, $this);
    }

    public function endPage($data = null) {
        if ($data === null) {
            $data = $this->makePage();
        }

        //Page::startRaw(true);//Start the output buffer, clean the old buffer.
        header_remove('Server');

        //Send Headers
        header($this->response->getHTTPVersion() . ' ' . $this->response->getCode() . ' ' . $this->response->getName());
        header('Content-Type: ' . $this->contentType->getMIMEType() . '; charset=' . $this->charset->getName() . ';');
        header('Content-Language: ' . $this->language->getLanguageCode());
        header('Date: ' . date('D, j M o H:i:s T', $this->date));
        header('X-Powered-By: ' . getConf('SITE_POWERED_BY'));
        header('Server: ' . getconf('SITE_SERVER'));
        if (definedconf('AC_ALLOW_HEADERS'))
            header('Access-Control-Allow-Headers: ' . getconf('AC_ALLOW_HEADERS'));
        if (definedconf('AC_ALLOW_ORIGIN') && isset($_SERVER['HTTP_ORIGIN'])) {
            $origin = $_SERVER['HTTP_ORIGIN'];
            foreach (getconf('AC_ALLOW_ORIGIN') as $k) {
                if ($k != $origin)
                    continue;
                header("Access-Control-Allow-Origin: " . $origin);
                break;
            }
        } else if(definedconf('AC_ALLOW_ORIGIN')) {
            
        }
        if (!$this->cachePage)
            header("Cache-Control: no-cache, must-revalidate");

        echo $data;

        ManagedConnection::closeAll();

        //Page::endRaw(false);//End and flush
        die(); //Stop.
    }

}
