<?php

if (!class_exists('ArrayList'))
    throw new Exception('The File class requires the ArrayList class to be imported first.');
if (!function_exists('endsWith'))
    throw new Exception('The File class requires the StringUtilities file to be imported first.');

define('PARENT_DIRECTORY', '..');

class File implements JsonSerializable {

    public static function getDirectorySeparator() {
        return DIRECTORY_SEPARATOR;
    }

    public static function fixSlashes($string) {
        $other_slash = static::getDirectorySeparator() == '/' ? '\\' : '/';
        $string = str_replace($other_slash, static::getDirectorySeparator(), $string);
        return $string;
    }

    /**
     * Returns the main directory.
     * 
     * @return \File
     */
    public static function getMainDirectory() {
        return new File(MAIN_FOLDER);
    }

    /**
     * Returns the top directory (Directory ABOVE /System/)
     * @return \File
     */
    public static function getTopDirectory() {
        return File::getMainDirectory()->getParent()->getChild('public');
    }

    /**
     * Returns the requested file (as defined by $currentpage (global)
     * @return \File
     */
    public static function getCurrentFile() {
        global $currentpage;
        return new File($currentpage);
    }

    /**
     * Returns the current working directory that the requested file is in.
     * @return \File
     */
    public static function getCurrentDirectory() {
        return static::getCurrentFile()->getParent();
    }

    /**
     * 
     * @return \File
     */
    public static function getDocumentRoot() {
        $h = $_SERVER['DOCUMENT_ROOT'];
        if (isset($_SERVER['SUBDOMAIN_DOCUMENT_ROOT']))
            $h = $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'];
        if (isset($_SERVER['REAL_DOCUMENT_ROOT']))
            $h = $_SERVER['REAL_DOCUMENT_ROOT'];
        $x = new File(static::fixSlashes($h));
        return $x->getAbsoluteDirectory();
    }

    public static function getTopDirectoryAsHTTP() {
        $docroot = static::getDocumentRoot();
        $top = static::getTopDirectory();

        $docroot = str_replace($docroot->getPath(), "", $top->getPath());
        return $docroot;
    }

    //Instance
    private $path;

    public function __construct($path, $parentdir = null) {
        $this->path = '';
        if ($parentdir !== null && $parentdir instanceof File)
            $this->path .= $parentdir->getPath() . File::getDirectorySeparator();
        $this->path .= $path;
    }

    public function isFile() {
        return is_file($this->path);
    }

    public function isDirectory() {
        return is_dir($this->path);
    }

    public function exists() {
        return file_exists($this->path);
    }

    /**
     * Functionally similar to exists() however searches and finds any 
     * file/directory with the same name (ignores file extensions).
     * @return type
     */
    public function nameExists() {
        $dir = $this->getParent();
        $dir_contents = $dir->getDirectoryContents();
        foreach ($dir_contents as $d) {
            if (!($d instanceof File))
                continue;
            if ($d->getNameWithoutExtension() == $this->getNameWithoutExtension())
                return true;
        }
        return false;
    }

    public function getPath() {
        return $this->path;
    }

    public function getName() {
        return basename($this->path);
    }

    public function getDirectoryName() {
        return dirname($this->path);
    }

    public function delete() {
        $x = unlink($this->path);
        if (!$x)
            throw new Exception("Failed to delete.");
    }

    public function getFileContents() {
        return file_get_contents($this->path);
    }

    public function mkdir() {
        if (!@mkdir($this->path))
            throw new Exception('Failed to mkdir.');
    }

    public function getSize() {
        return filesize($this->path);
    }

    public function getFileExtension() {
        $name = $this->getName();
        if (!str_contains('.', $name)) {
            return '';
        }

        $name_split = explode('.', $name);
        return $name_split[sizeof($name_split) - 1];
    }
    
    /**
     * 
     * @return ContentType
     */
    public function getContentType() {
        return ContentType::getByExtension($this->getFileExtension());
    }

    public function append($string) {
        if ($this->exists()) {
            file_put_contents($this->path, $string, FILE_APPEND);
        } else {
            file_put_contents($this->path, $string);
        }
    }

    public function getNameWithoutExtension() {
        $name = $this->getName();
        return str_replace_last('.' . $this->getFileExtension(), '', $name);
    }

    /**
     * A very powerful function, this is basically used to refer to a file that
     * resides somewhere in the /System/ server directory that can be imported
     * via any HTTP method such as HTML, Javascript, CSS etc etc etc.
     * 
     * e.g.
     * echo '<script type="text/javascript" src="'.new File('C:\www\root\System\Templates\MyTemplate\scripts\test.js')->getAsHTTPPath().'"></script>';
     * 
     * Yes, it's that easy.
     */
    public function getAsHTTPPath() {
        //First, confirm $this is within the system dir.
        $my_path = $this->getPath();
        $main_path = File::getDocumentRoot()->getPath();
        //if(!startsWith($my_path, File::getDocumentRoot()->getPath())) throw new Exception('File must be in the System Directory'); TODO: Fix
        //Now we need to first remove the main dir
        $my_path = str_replace($main_path, '', $my_path);

        //Now Escape HTTP slashes
        $my_path = str_replace('\\', '/', $my_path);

        //And return with the '/System/' dir
        return $my_path;
    }

    /**
     * 
     * @return File
     */
    public function getAbsoluteDirectory() {
        return new File(realpath($this->path));
    }

    /**
     * Gets the parent directory.
     * 
     * @return File
     */
    public function getParent() {
        return new File(dirname($this->path));
    }

    /**
     * 
     * @param type $x
     * @return \File
     */
    public function getChild($x) {
        return new File($x, $this);
    }

    public function contains($name) {
        return file_exists($this->path . File::getDirectorySeparator() . $name);
    }

    public function getDirectoryContents($filter = null, $directories_only = false, $files_only = false) {
        if (!$this->isDirectory())
            throw new Exception('Not a directory.');
        $list = new ArrayList('File');

        foreach (scandir($this->path) as $file) {
            if ($file === '.' || $file === '..')
                continue;
            if ($filter !== null && !endsWith($file, $filter))
                continue;
            $f = new File($file, $this);
            if ($directories_only && !$f->isDirectory())
                continue;
            if ($files_only && !$f->isFile())
                continue;
            $list->add($f);
        }

        return $list;
    }

    public function getDirectoryContentsRecursive() {
        if (!$this->isDirectory())
            throw new Exception('Not a directory.');
        $list = new ArrayList('File');

        foreach (scandir($this->path) as $file) {
            if ($file === '.' || $file === '..')
                continue;
            $f = new File($file, $this);
            $list->add($f);
            if ($f->isDirectory())
                $list->add($f->getDirectoryContentsRecursive());
        }

        return $list;
    }
    
    /**
     * 
     * @return \File
     */
    public function createIfNotExists() {
        if($this->exists()) return $this;
        $this->mkdir();
        return $this;
    }
    
    public function copyTo($file) {
        if(!($file instanceof File)) throw new \Exception("Invalid File.");
        if($file->isDirectory()) {
            $file = $file->getChild($this->getName());
        }
        
        copy($this->path, $file->path);
        return $file;
    }

    public function jsonSerialize() {
        return array(
            'type' => ($this->isDirectory() ? 'directory' : 'file'),
            'path' => $this->path
        );
    }

    public function __toString() {
        return json_encode($this);
    }

}
