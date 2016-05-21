<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();
class Color {
    public static function BLACK() {return new Color(0, 0, 0);}
    public static function WHITE() {return new Color(1,1,1);}
    
    //Instance
    public $red;
    public $green;
    public $blue;
    public $alpha;
    private $hex_passed;
    
    /**
     * Create a new Color. Valid formats are either String HEXADECIMAL codes, or
     * RGB/RGBA formats.
     * e.g. To create white you could do:
     *  new Color('#FFFFFF');
     *  new Color('FFFFFF');
     *  new Color('FFF');
     *  new Color('#FFF');
     *  new Color(1, 1, 1, 1);
     *  new Color(1, 1, 1);
     *  new Color(255, 255, 255, 1);
     *  new Color(255, 255, 255, 100);
     *  new Color(255, 255, 255);
     * 
     * @param mixed $r
     * @param int $g
     * @param int $b
     * @param int $a
     */
    public function __construct($r=1,$g=null,$b=1,$a=1) {
        if($r instanceof Color) {
            $this->red = $r->red;
            $this->green = $r->green;
            $this->blue = $r->blue;
            $this->alpha = $r->alpha;
            $this->reclampColors();
        } else {
            //First, check if this is a string or a RGB/RGBA format
            $hex = false;
            if(!is_float($r)) {//MAY be a hex code.
                //MAY be RGBA
                $x = preg_replace("/[^0-9A-Fa-f]/", '', $r); // Gets a proper hex string
                if(!is_numeric($r) && ($g === null || !is_numeric($g))) {//Color is a hex code
                    $hex = true;
                    $r = $x;
                } else {
                    //Must be some kind of RGB in string format
                    $r = floatval($x);
                    $g = floatval($g);
                    $b = floatval($b);
                    $a = floatval($a);
                }
            }

            if($hex) $this->hex_passed = $r;
            if($hex) {
                $hexcode = $r;
                if(strlen($hexcode) === 3) {//3 decimal HEX
                    $r = hexdec(str_repeat(substr($hexcode, 0, 1), 2));
                    $g = hexdec(str_repeat(substr($hexcode, 1, 1), 2));
                    $b = hexdec(str_repeat(substr($hexcode, 2, 1), 2));
                } else {
                    $colorVal = hexdec($hexcode);
                    $r = 0xFF & ($colorVal >> 0x10);
                    $g = 0xFF & ($colorVal >> 0x8);
                    $b = 0xFF & $colorVal;
                }
            }

            $this->red = &$r;
            $this->green = &$g;
            $this->blue = &$b;
            $this->alpha = &$a;


            //Now check if they're floating point values, or /255 values
            if($r === 1 && $a > 1) $r *= 255.0;
            if($g === 1 && $a > 1) $r *= 255.0;
            if($b === 1 && $a > 1) $r *= 255.0;

            if($r > 1) $r = floatval($r) / 255.0;
            if($g > 1) $g = floatval($g) / 255.0;
            if($b > 1) $b = floatval($b) / 255.0;
            if($a > 1) $a = floatval($a) / 100.0;

            //Now we should have our values rougly, validate them.
            $this->reclampColors();
        }
    }
    
    public function reclampColors() {
        $this->red = min(max($this->red, 0), 1.0);
        $this->blue = min(max($this->blue, 0), 1.0);
        $this->green = min(max($this->green, 0), 1.0);
        return $this;
    }
    
    public function round() {
        $this->red = round($this->red*255.0)/255.0;
        $this->green = round($this->green*255.0)/255.0;
        $this->blue = round($this->blue*255.0)/255.0;
        $this->alpha = round($this->alpha*100.0)/100.0;
        return $this->reclampColors();
    }
    
    /**
     * 
     * @param float $a
     * @return Color
     * @throws Exception
     */
    public function setAlpha($a) {
        if(!is_float($a)) $a = floatval($a);
        if($a > 1) $a /= 100.0;
        if($a < 0) throw new Exception('Invalid Alpha (Less than Zero)');
        if($a > 1) throw new Exception('Invaid Alpha (Greater than 100)');
        $this->alpha = $a;
        return $this;
    }
    
    public function toString($mult=1) {
        $x = $this->red . ', ' . $this->green . ', ' . $this->blue . ', ' . $this->alpha;
        if(isset($this->hex_passed)) $x .= ', '.$this->hex_passed;
        return $x;
    }
    
    public function duplicate() {
        return new Color($this);
    }
    
    public function lighten($amt, $doAlpha=false) {
        $this->red *= $amt;
        $this->green *= $amt;
        $this->blue *= $amt;
        if($doAlpha) $this->alpha *= $amt;
        
        $this->reclampColors();
        return $this;
    }
    
    public function toCSS() {
        $x = 'rgb';
        if($this->alpha != 1) $x .= 'a';
        $x .= '(';
        $x .= round($this->red * 255) . ',';
        $x .= round($this->green * 255) . ',';
        $x .= round($this->blue * 255);
        if($this->alpha != 1) $x .= ',' . round($this->alpha * 100);
        $x .= ')';
        return $x;
    }
}
