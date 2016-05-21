<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

abstract class Addon {
    const ADDONS_DIRECTORY = "Addons";
    private static $addons;//Stores the loaded addons
    
    public static function getAllAddons() {return self::$addons->createCopy();}
    
    //Static
    public static function loadAddons() {
        //Loads all the addons into memory for later processing.
        $path = MAIN_FOLDER.File::getDirectorySeparator().Addon::ADDONS_DIRECTORY;

        //Search for a list of files in the given directory
        $file = new File($path);
        $directories = $file->getDirectoryContents();
        
        $loaded_addons = new ArrayList('Addon');
        
        foreach($directories as $dir) {
            //$dir typeof File
            $addon=true;
            $file = new File($dir->getName() . 'Addon' . SCRIPT_EXTENSION, $dir);
            if(!$file->exists()) {
                $addon=false;
                $file = new File($dir->getName() . SCRIPT_EXTENSION, $dir);
            }
            
            //Now we have the file we can import it.
            import($file);
            if($addon) {
                $name = $dir->getName().'\\'.$dir->getName() . 'Addon';
            } else {
                $name = $dir->getName().'\\'.$dir->getName();
            }
            try {
                $x = new $name();
                $loaded_addons->add($x);
            } catch(Exception $e) {
                pageException($e);
            }
        }
        
        //Now "enable" the addons
        $loaded_addons->sortByFunctionValue(array('getDependancies', 'size'), array(), true);
        foreach($loaded_addons->createCopy() as $l) {
            if(!($l instanceof Addon)) continue;
            if(!$l->getDependancies()->isEmpty()) break;
            try {
                $l->enable();
            } catch(Exception $e) {
                pageException($e);
            }
            $loaded_addons->remove($l);
        }
        
        //Addons remaining "require" another addon.
        while(!$loaded_addons->isEmpty()) {
            $loaded_addons->get(0)->loadDependancies($loaded_addons);
        }
        
        return;
    }
    
    /**
     * Finds the addon by name suppied.
     * 
     * @param string $name
     * @return Addon
     */
    public static function getAddonByName($name) {
        if(!isset(Addon::$addons)) return null;
        $list = Addon::$addons;
        if(!($list instanceof ArrayList)) return null;
        return $list->getByFunctionValue('getName', $name);
    }
    
    //Instance
    private $name;
    private $version;
    private $enabled = false;
    
    public function __construct($name, $version) {
        if(!isset(Addon::$addons)) Addon::$addons = new ArrayList('Addon');
        
        $this->name = $name;
        $this->version = $version;
        
        Addon::$addons->add($this);
    }
    
    public function getName() {return $this->name;}
    public function getVersion() {return $this->version;}
    
    /**
     * Returns an ArrayList of addons that this addon is dependant upon. Please
     * supply string names only.
     * 
     * @return ArrayList
     */
    public function getDependancies() {return new ArrayList();}
    
    public function isEnabled() {return $this->enabled;}
    
    /**
     * 
     * @param ArrayList $list
     */
    private function loadDependancies(&$list) {
        foreach($this->getDependancies() as $l) {
            $a = $list->getByFunctionValue('getName', $l);
            if(!($a instanceof Addon)) continue;
            if($a->getDependancies()->isEmpty()) {
                $a->enable();
                $list->remove($a);
            } else {
                $a->loadDependancies($list);
                $list->remove($a);
            }
        }
        $this->enable();
        $list->remove($this);
    }
    
    public function getFolder() {
        return new File(Addon::ADDONS_DIRECTORY.File::getDirectorySeparator().$this->name, File::getMainDirectory());
    }
    
    public function import($name) {
        import(Addon::ADDONS_DIRECTORY.File::getDirectorySeparator() . $this->name . '.' . $name);
    }
    
    
    private function enable() {
        if($this->isEnabled()) return;
        $this->onEnable();
        $this->enabled = true;
    }
    
    public abstract function onEnable();
}