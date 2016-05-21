<?php
/*
 * Main PHP File, import STANDARD setup files needed on ALL pages!
 */

/**
 * Start by defining the simple definition that all pages are using to track me.
 */
define('SYSTEM_VERSION', '1.00');
define('SCRIPT_LOADTIME', microtime(true));//Used for time checking, not 100% accurate, but good for an average.
define('MAIN_INCLUDED', true);//Validation Checking
define('SCRIPT_EXTENSION', '.php');//Used for imports
define('MAIN_FOLDER', dirname(__FILE__));//Used by the File Class.
define('NEWLINE', "\n");//Just a bit prettier
define('CRLF', "\r\n");
define('CLI', PHP_SAPI === 'cli');

//Require the ArrayList Class, used by the import class itself.
require_once MAIN_FOLDER.DIRECTORY_SEPARATOR.'ArrayList'.DIRECTORY_SEPARATOR.'ArrayList'.SCRIPT_EXTENSION;
//Require the StringUtilities PHP file, used by the import class itself.
require_once MAIN_FOLDER.DIRECTORY_SEPARATOR.'Utilities'.DIRECTORY_SEPARATOR.'StringUtilities'.SCRIPT_EXTENSION;
//Require the File class, used by the import class itself.
require_once MAIN_FOLDER.DIRECTORY_SEPARATOR.'File'.DIRECTORY_SEPARATOR.'File'.SCRIPT_EXTENSION;

/*
 * Setup Globals
 */
global
    $imported_classes,                  //Holds the successfully imported classes
    $currentpage
;

if(!isset($currentpage)) {
    die('This file has been injected incorrectly.');
}

/**
 * Store imported classes in this array list for tracking if need be.
 */
$imported_classes = new ArrayList();

/*
 * Setup Global Functions
 */
function isValidClassName($name) {
    return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
}

/**
 * Import the Class located at $classPath within the System Folder.
 * Example:
 *      import('Page.Page');
 * to import the Page class. Wildcards can be used to import all classes within
 * a specified folder (Not sub directories though!)
 * 
 * @param string $classPath 
 */
function import($path, $require_once=true) {
    $original_path = $path;
    
    if($path instanceof File) {
        /*
         * Since the import is a file, we need to make sure that the import is
         * in the system folder (For security reasons)
         */
        $main_file = File::getMainDirectory();
        if(!startsWith($path->getPath(), $main_file->getPath() . File::getDirectorySeparator())) {
            throw new Exception('Security Exception, cannot import a class outside the System directory.');
        }
        
        $original_path = $path->getPath();
        //Now it's safe to import
        $path = $path->getPath();
    } else {
        $path = str_replace('.', File::getDirectorySeparator(), $path);
        $path = MAIN_FOLDER.File::getDirectorySeparator().$path;
        
        if(endsWith($path, File::getDirectorySeparator().'*')) {
            //Search for a list of files in the given directory

            $path = str_replace_last(File::getDirectorySeparator().'*', '',  $path);
            $file = new File($path);
            
            try {
                foreach($file->getDirectoryContents(SCRIPT_EXTENSION, false, true) as $file) {
                    import($file);//Imports
                }
            } catch(Exception $e) {
                throw new Exception('Failed to import ' . $file->getName(), 0, $e);
            }
            
            return;
        } else {
            $path .= SCRIPT_EXTENSION;
        }
    }
    
    
    
    //Import Global
    global $imported_classes;
    
    if(!file_exists($path)) throw new Exception('Failed to import "' . $path . '", File does not exist.');
    /*
     * To the question of why I'm not using require_once and require instead of
     * this if statement, refer to Style/Stylesheet.php for a bit of an idea.
     * 
     * Basically the file can handle without requiring the main on load, but PHP
     * considers it loaded.
     * 
     * example:
     *  Main.PHP~
     *      define('MAIN_INCLUDED', true);
     *      function import($name) {
     *          require_once $name;
     *      }
     * 
     *  ClassA.php~
     *      if(!defined('MAIN_INCLUDED')) {
     *          require_once('Main.php');
     *          import('ClassB.php');
     *          $o = new B();
     *      }
     * 
     *      class A { }
     *      
     *  ClassB.php~
     *      if(!defined('MAIN_INCLUDED')) throw new Exception();
     *      import('ClassA.php');
     *      class B extends A {}
     * 
     * If the script Class A were loaded first, then it would import the Main
     * file before trying to create an instance of B. This then causes an error
     * because the instance of B is created before the class definition of A is
     * made.
     * 
     * This is to be expected in theory, however causes a mismatch because of 
     * Class B trying to import class A.
     * 
     * Ideally if you're in this situation (As Per Stylesheet.php) you want to
     * stop double-declaration of the class.
     * 
     * Example A:
     *  Main.php~
     *      define('MAIN_INCLUDED', true);
     *      require_once 'ArrayList/ArrayList.php';
     *      $importedObjs = new ArrayList();
     *      function import($name) {
     *          global $importedObjs;
     *          if($importedObjs->contains($name)) return;//Do Nothing
     *          $importedObjs->add($name);//Add to the list BEFORE requiring
     *          require $name;
     *      }
     * 
     * ClassA.php~
     *      if(!defined('MAIN_INCLUDED')) {
     *          require_once 'Main.php';
     *          import('ClassB.php');
     *          $obj = new ClassB();
     *          echo $obj->getHigh() . ', ' . $obj->getLow());
     *          exit();//Stop Processing, or the class will be declared twice!
     *      }
     *
     *      //Alternatively you could wrap the entire class definition in an if
     *      //statement with a class_exists function call
     *      class A {
     *          public function __construct() {}
     *          public function getLow() {return 'James';}
     *      }
     * 
     * ClassB.php~
     *      if(!defined('MAIN_INCLUDED')) throw new Exception();
     *      import('ClassA.php');
     *      class B extends A {
     *          public function __construct() {parent::__construct();}
     *          public function getHigh() {return 'Hello';}
     *      }
     * 
     * This is essentially what happens now, it stops classes being defined 
     * twice, but still maintaining the import structure similar to Java.
     * 
     * It is slightly less efficient however it is how I see fit.
     */
    if($require_once && !$imported_classes->contains($path)) {
        $imported_classes->add($path);
        require $path;
    } else if(!$require_once) {
        require $path;
    }
}

/**
 * 
 * @return \File
 */
function getRequestURI() {
    global $currentpage;
    $file = new File($currentpage);
    return $file;
}

/*
 * Global Imports
 */
import('Configuration.Configuration');
import('Config.Config');

/*
 * Setup
 */
date_default_timezone_set(getconf('SERVER_TIMEZONE'));
session_start();//I need to remove this at some stage.

//Setup Error Handling
function pageError ($errno=-1, $errstr=null, $errfile=null, $errline=-1, $errcontext=null) {
    try {
        import('Page.Page');
        
        $page = Page::getPage();
        $page->echoData($page->getTemplate()->genError($errno, $errstr, $errfile, $errline, $errcontext));
        $page->makePage();
        $page->endPage();
    } catch(Exception $e) {
        echo $errno . ':' . $errstr . ':' . $errfile . ':' . $errline . ':' . $errcontext . ':'.NEWLINE.'<br />';
        die('System Error Occured... ' . ($e == null ? '' : $e->getMessage()));
    }
}
function pageException ($x) {
    pageError($x);
}

if(getconf('DEBUG_MODE')) {
    //error_reporting(E_ALL);
} else {
    // Turn off all error reporting
    //error_reporting(0);
}

set_error_handler('pageError');
set_exception_handler('pageException');

/*
 * Load Addons
 */
import('Addon.Addon');
Addon::loadAddons();

/*
 * Now fire the event
 */
import('Events.PageRequestEvent');
$evt = new PageRequestEvent();
$evt->fire();