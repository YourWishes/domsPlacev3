<?php
class ContentType {
    private static $TYPES = array();
    
    //Defnie Statics
    public static $TEXT_HTML;
    public static $TEXT_CSS;
    public static $TEXT_JAVASCRIPT;
    
    public static $APPLICATION_JSON;
    public static $APPLICATION_JAVASCRIPT;
    public static $APPLICATION_OCTET_STREAM;
    
    public static $IMAGE_PNG;
    public static $IMAGE_JPG;
    public static $IMAGE_JPEG;
    public static $IMAGE_GIF;
    public static $IMAGE_BMP;
    public static $IMAGE_TIFF;
    
    public static $VIDEO_MP4;
    
    //Static Methods
    /**
     * 
     * @param string $mime
     * @return ContentType
     */
    public static function getByMime($mime) {
        foreach(ContentType::$TYPES as $type) {
            if(strtolower($type->mime) == strtolower($mime)) return $type;
        }
        return static::$APPLICATION_OCTET_STREAM;//Basically the fallback
    }
    
    /**
     * 
     * @param string $ext
     * @return ContentType
     */
    public static function getByExtension($ext) {
        if(!startsWith($ext, '.')) $ext = '.' . $ext;
        foreach(ContentType::$TYPES as $type) {
            if(strtolower($type->extension) == strtolower($ext)) return $type;
        }
        return static::$APPLICATION_OCTET_STREAM;//Basically the fallback
    }
    
    public static function getDefault() {return ContentType::getByMime(getconf('DEFAULT_CONTENT_TYPE'));}
    public static function getStylesetDefault() {return ContentType::getByMime(getconf('DEFAULT_STYLESET_TYPE'));}
    
    //Instance
    private $name;
    private $description;
    private $mime;
    private $extension;
    
    public function __construct($name, $description, $mime, $extension) {
        $this->name = $name;
        $this->description = $description;
        $this->mime = $mime;
        $this->extension = $extension;
        
        array_push(ContentType::$TYPES, $this);
    }
    
    public function getName() {return $this->name;}
    public function getDescription() {return $this->description;}
    public function getMIMEType() {return $this->mime;}
    public function getExtension() {return $this->extension;}
    public function getCategory() {
        return explode('/', $this->getMIMEType())[0];
    }
}

//Instance Statics
ContentType::$TEXT_HTML = new ContentType('HTML Text', 'Text HTML', 'text/html', '.html');
ContentType::$TEXT_CSS = new ContentType('CSS Styleset', 'Text CSS Style Definitions', 'text/css', '.css');
ContentType::$TEXT_JAVASCRIPT = new ContentType('Javascript Source', 'Text Javascript Source Code', 'text/javascript', '.js');

ContentType::$APPLICATION_JSON = new ContentType('JSON', 'Text Javascript Object Notation for JSON', 'application/json', '.json');
ContentType::$APPLICATION_JAVASCRIPT = new ContentType('Javascript Object Notation', 'Text Javascript Object Notation for JSONP', 'application/javascript', '.jsonp');
ContentType::$APPLICATION_OCTET_STREAM = new ContentType('Octet Stream', 'Application Octet Stream', 'application/octet-stream', null);

ContentType::$IMAGE_PNG = new ContentType('PNG Image', 'Portable Network Graphic', 'image/png', '.png');
ContentType::$IMAGE_JPG = new ContentType('JPG Image', 'Joint Photographic Experts Group Image', 'image/jpg', '.jpg');
ContentType::$IMAGE_JPEG = new ContentType('JPEG Image', 'Joint Photographic Experts Group Image', 'image/jpeg', '.jpeg');
ContentType::$IMAGE_GIF = new ContentType('GIF Image', 'Graphics Interchange Format', 'image/gif', '.gif');
ContentType::$IMAGE_BMP = new ContentType('BMP Image', 'Bitmap Image', 'image/bmp', '.bmp');
ContentType::$IMAGE_TIFF = new ContentType('TIFF Image', 'Tagged Image File Format', 'image/tiff', '.tiff');


ContentType::$VIDEO_MP4 = new ContentType('MP4 Video', 'Moving Picture Experts Group', 'video/mp4', '.mp4');
