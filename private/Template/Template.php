<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Configuration.Configuration');
import('Page.Page');
import('Template.Component.TemplateComponent');

class Template {
    private static $TAB_COUNT = 4;
    private static $IMPORTED_TEMPLATES = array();
    
    /**
     * 
     * @param type $name
     * @return Template
     * @throws Exception
     */
    public static function getSystemTemplate($name) {
        if(isset(Template::$IMPORTED_TEMPLATES[$name])) return Template::$IMPORTED_TEMPLATES[$name];
        if(!Template::isValidTemplateName($name)) throw new Exception('Template name is invalid.');
        try {
            Template::importTemplateClass($name);
            $instance = new $name();
            if(!($instance instanceof Template)) throw new Exception('Supplied Template must be a valid Template Class.');
            Template::$IMPORTED_TEMPLATES[$name] = $instance;
        } catch(Exception $e) {
            throw new Exception('Failed to import template.', null, $e);
        }
        return Template::$IMPORTED_TEMPLATES[$name];
    }
    
    public static function importTemplateClass($name) {
        import('Templates.' . $name . '.'.$name.'');
    }
    
    public static function getTemplateFromConfig($key) {
        if(!definedconf($key)) throw new Exception('Template not defined.');
        return Template::getSystemTemplate(getconf($key));
    }
    
    public static function getDefault() {
        return Template::getTemplateFromConfig('TEMPLATE_DEFAULT');
    }
    
    /**
     * Checks if a String is valid for a Template name.
     * 
     * @param string $name
     */
    public static function isValidTemplateName($name) {
        if(!isValidClassName($name)) return false;
        return true;
    }
    
    //Instance
    private $name;
    private $version;
    private $directory;
    private $date;
    
    private $configurations;
    
    private $components;
    
    public function __construct($name, $version, $directory, $date) {
        $this->name = $name;
        $this->version = $version;
        $this->directory = new File($directory);
        
        if(!($date instanceof DateTime)) {
            $date2 = new DateTime();
            $date2->setTimestamp($date);
            $date = $date2;
        }
        
        $this->date = $date;
        
        $this->configurations = new Configuration();
        
        $this->components = new ArrayList('TemplateComponent');
    }
    
    public function getName() {return $this->name;}
    public function getVersion() {return $this->version;}
    /**
     * 
     * @return File
     */
    public function getDirectory() {return $this->directory->getParent();}
    public function getDate() {return $this->date;}
    
    /**
     * 
     * @return Configuration
     */
    public function getConfigurations() {
        return $this->configurations;
    }
    
    /**
     * Returns this templates file... E.g. for a Template named "YouWish" we 
     * would return new File('/System/Templates/YouWishTF/YouWishTF.php');
     * 
     * @return \File
     */
    public function getTemplateFile() {
        return new File($this->name . SCRIPT_EXTENSION, $this->getFolder());
    }
    
    /**
     * Returns this folder.
     * 
     * @return \File
     */
    public function getFolder() {
        return new File('Templates' . File::getDirectorySeparator() . $this->name, File::getMainDirectory());
    }
    
    /**
     * Returns a file in the template directory.
     * 
     * @param string $sub_dir
     * @return \File
     */
    public function getFile($sub_dir) {
        return new File($sub_dir, $this->getFolder());
    }

    /**
     * 
     * @param Page $page
     */
    public function startPage(&$page) {}
    
    /**
     * 
     * @param string $data
     * @param Page $page
     * @return string
     */
    public function make($data, &$page) {return $data;}
    
    
    //Generation Methods, meant to be overriden but will work regardless
    public function genError($errno=-1, $errstr=null, $errfile=null, $errline=-1, $errcontext=null) {
        $exception_string = '';
        if($errno instanceof Exception) {
            $e = $errno;
            
            $exception_string .= 'Exception "' . $e->getMessage(). '" caught in script ' .$e->getFile(). ': ' . $e->getLine();
            $exception_string .= $this->generateNewLine();
            
            foreach($e->getTrace() as $k => $l) {
                $exception_string .= $this->generateTab();
                if(isset($l["file"])) $exception_string .= 'at ' . $l["file"] . '::';
                if(isset($l["class"])) $exception_string .= 'class::' . $l["class"];
                if(isset($l["type"])) {
                    $exception_string .= $l["type"];
                    if(isset($l["function"])) {
                        $exception_string .= $l["function"] . ':';
                    }
                } else {
                    $exception_string .= '::';
                    if(isset($l["function"])) {
                        $exception_string .= 'function(' . $l["function"] . ')';
                    }
                }
                if(isset($l["line"])) $exception_string .= $l["line"];
                $exception_string .= $this->generateNewLine();
            
                if($e->getPrevious() instanceof Exception) {
                    $exception_string .= 'Previous; ' . $this->genError($e->getPrevious());
                }
            }
        } else {
            //$exception_string .= 'Error "' . $errstr. '" caught in script ' . $errfile . ': ' . $errline;
            $exception_string .= $errstr;
        }
        
        return $exception_string;
    }
    
    public function generateNewLine() {
        return '<br />' . PHP_EOL;
    }
    
    public function generateTab() {
        $x = '';
        for($i = 0; $i < Template::$TAB_COUNT; $i++) {
            $x .= '&nbsp;';     
        }
        return $x;
    }
    
    /**
     * Imports a .php file from the Components section of this template.
     * 
     * @param string $component_name
     * @return TemplateComponent
     */
    public function importComponent($component_name) {
        //First, run an import
        try {
            //Using the false parameter to import multiple times over.
            //Ensure template component is set to handle this.
            $previous = TemplateComponent::getLastLoaded();
            import('Templates.' . $this->getName() . '.Components.' . $component_name, false);
            $new = TemplateComponent::getLastLoaded();
            if($new === $previous) throw new Exception();
        } catch (Exception $e) {
            throw new Exception('Template Component not found.', 206, $e);
        }
        
        $comp = TemplateComponent::getLastLoaded();
        $comp->construct($this);
        
        $this->components->add($comp);
        
        //Now the component is stored as the last loaded TemplateComponent
        return $comp;
    }
    
    public function getComponent($component_name) {
        $x = $this->components->getByFunctionValue('getName', $component_name);
        if($x == null) {
            $x = $this->importComponent($component_name);
        }
        return $x;
    }
    
    /**
     * Imports a .php file from within the .Classes. Folder
     * @param string $name
     * @param string $sub_dir Sub Direcotry within the /Templates/%name%/ folder
     */
    public function importClass($name, $sub_dir='Classes') {
        import('Templates.' . $this->getName() . '.'.$sub_dir.'.' . $name);
    }
    
    /**
     * Imports a .php file from within the .Pages. Folder
     * @param string $name
     * @param string $sub_dir Sub Direcotry within the /Templates/%name%/ folder
     */
    public function importPage($name, $sub_dir='Pages') {
        import('Templates.' . $this->getName() . '.'.$sub_dir.'.' . $name);
    }
    
    /**
     * Imports a .php file from within the .Stylesheets. Folder
     * 
     * @param string $name
     */
    public function importStylesheet($name) {
        $this->importClass($name, 'Stylesheets');
    }
    
    /**
     * 
     * @param type $image_name
     * @return Image
     */
    public function importImage($image_name) {
        $image_file = new ImageFile(dirname($this->getDirectory()) . DIRECTORY_SEPARATOR . 'Images' . DIRECTORY_SEPARATOR . $image_name);
        return $image_file;
    }
    
    /**
     * Turns any supplied ArrayList or array into a HTML5 valid table. A few 
     * things to note on this function, first is that this function is dependant
     * on the second argument to supply the Table Matrix.
     * 
     * How does the Table Matrix work? Since the array needs some list of 
     * information to pull out of the items that are in the array.
     * 
     * So for example if your array looks like this:
     *      $arr = [
     *          {name: "My Name", date: "My Date", password: "Some Secret"},
     *          {name: "Start Name", date: "Some Date", password: "My Secret"},
     *          {name: "Some Name", date: "End Date", password: "Secret"}
     *      ];
     * 
     * Then most likely your table Matrix would be something like this:
     *      $matrix = [
     *          {name: "Name", method: "getName"},
     *          {date: "Date", method: "getDate"}
     *      ];
     * 
     * Why use this? Well two reasons, most obvious to help formatting, the 
     * other is to stop unwanted data being leaked into the table (In this case
     * the object $passsword)
     * 
     * @param ArrayList|array $table_data
     * @param ArrayList|array $table_matrix
     * @param ArrayList|array $classes
     * @param string $id
     * @param int $maxlength
     */
    public function generateTable($table_data, $table_matrix, $classes=null, $id=null, $maxlength=-1) {
        
        //Validate
        if(!is_array($table_data) && !($table_data instanceof ArrayList)) throw new Exception('Table Data must be an Array/ArrayList');
        if(!is_array($table_matrix) && !($table_matrix instanceof ArrayList)) throw new Exception('Table Matrix is invalid.');
        
        //Translate
        if(is_array($table_data)) $table_data = new ArrayList($table_data);
        if(is_array($table_matrix)) $table_matrix = new ArrayList($table_matrix);
        
        //Coordinate (Fuck dude I'm smoooooth)
        $x = '<table';
        if($classes !== null) {
            if(is_string($classes)) {
                $x .= ' class="' . $classes . '"';
            } else if(is_array($classes) || $classes instanceof ArrayList) {
                if(is_array($classes)) $classes = new ArrayList($classes);
                $x .= ' class="' . $classes->implode(' ') . '"';
            }
        }
        
        if($id !== null && is_string($id)) {
            $x .= ' id="' . $id . '"';
        }
        $x .= '>';
        
        //Now we can form our table header
        $x .= '<thead><tr>';
        foreach($table_matrix as $thead) {
            if(!is_array($thead) && !($thead instanceof ArrayList)) throw new Exception('Malformed table matrix.');
            if(is_array($thead)) $thead = new ArrayList($thead);
            
            if(!$thead->isKeySet('name')) throw new Exception('Malformed table matrix. (Need col name)');
            if(!$thead->isKeySet('method')) throw new Exception('Malformed table matrix. (Need col method)');
            
            $x .= '<th>' . $thead["name"] . '</th>';
        }
        $x .= '</tr></thead>';
        
        //Now the table data.
        $x .= '<tbody>';
        for($i = 0; $i < $table_data->size(); $i++) {
            $obj = $table_data[$i];
            
            //Now iterate over the matrix
            $x .= '<tr>';
            foreach($table_matrix as $thead) {
                $args = array();
                if(is_array($thead)) $thead = new ArrayList($thead);
                if($thead->isKeySet("args")) $args = $def["args"];
                
                $method = $thead["method"];
                $result;//Definition
                if(is_array($method) || $method instanceof ArrayList) {
                    if(is_array($method)) $method = new ArrayList($method);
                    /*
                     * If $method is an array then the expected result from a
                     * call_user_func_array on $array[0] is another object.
                     * 
                     * Basically we're going to run through until we reach 
                     * $method->size() on each returned object.
                     * 
                     * e.g. if I have the following:
                     * {
                     *      getChild() {
                     *          return {getName() {"Simon"}};
                     *      }
                     * }
                     * 
                     * I would have the following $method:
                     * ["getChild", "getName"]
                     * 
                     * To overcomplicate again, if I had:
                     *  {
                     *      getChild() {
                     *          return {
                     *              getButt() {
                     *                  return {getName() { "Simon"}};
                     *              }
                     *          };
                     *      }
                     *  }
                     * 
                     * And my $method would be:
                     * ["getChild", "getButt", "getName"]
                     * 
                     * TECHNICALLY speaking you can supply args, I will make
                     * this better in the future but for now I wouldn't.
                     */
                    
                    $result = $obj;
                    foreach($method as $func) {
                        $result = call_user_func_array(array($result, $func), $args);
                    }
                } else {
                
                    $result = call_user_func_array(array($obj, $thead["method"]), $args);

                }
                //HTML Fix up for I.E.
                if($result === null || !isset($result) || $result == '') $result = '&nbsp;';
                
                $x .= '<td>' . $result . '</td>';
            }
            $x .= '</tr>';
            
            if($maxlength != -1 && $i >= $maxlength) break;
        }
        $x .= '</tbody>';
        
        //Finally close up the table.
        $x .= '</table>';
        return $x;
    }
    
    /**
     * Generates a panel for a group of information to sit in. I will be adding
     * to this function later for more options.
     * 
     * @param mixed $data Data to be injected into the panel-body
     * @param string $header Header string (or null for none) to be injected.
     * @param string $footer Footer string (or null for none) to be injected.
     * @return string The generated Panel HTML5 markup
     */
    public function generatePanel($data, $header=null, $footer=null, $extra_classes=array('panel-default')) {
        //Using bootstraps built in Panels.
        if(!($extra_classes instanceof ArrayList)) $extra_classes = new ArrayList($extra_classes);
        
        $x = '';
        $x .= '<div class="panel '.$extra_classes->implode(' ').'">';
        
        //Check for a header
        if($header !== null) {
            $x .= '<div class="panel-heading">'.$header.'</div>';
        }
        $x .= '<div class="panel-body">'.$data.'</div>';
        
        //Check for a footer
        if($footer !== null) {
            $x .= '<div class="panel-footer">'.$footer.'</div>';
        }
        
        $x .= '</div>';
        return $x;
    }
    
    public function generateModal($name, $body, $header='Modal', $closeButton=true, $footer='', $large = false) {
        $x = '';
        $x .= '<div class="modal fade" id="'.$name.'" tabindex="-1" role="dialog">';
        $x .= '<div class="modal-dialog '.($large ? 'modal-lg' : '').'">';
        $x .= '<div class="modal-content">';
        
        $x .= '<div class="modal-header">';
        if($closeButton) $x .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        $x .= '<h4 class="modal-title">';
        $x .= $header;
        $x .= '</h4>';
        $x .= '</div>';
        
        $x .= '<div class="modal-body">';
        $x .= $body;
        $x .= '</div>';
        
        $x .= '<div class="modal-footer">';
        $x .= $footer;
        $x .= '</div>';
        
        $x .= '</div>';
        $x .= '</div>';
        $x .= '</div>';
        return $x;
    }
    
    public function escapeHTML($x) {
        if(!is_string($x)) $x = '' . $x;
        return htmlspecialchars($x, ENT_HTML5|ENT_COMPAT);
    }
    
    public function formatTime($datetime) {
        if(!($datetime instanceof \DateTime)) throw new \Exception("Invalid DateTime.");
        return $datetime->format('g:ia \o\n l jS F Y');
    }
}