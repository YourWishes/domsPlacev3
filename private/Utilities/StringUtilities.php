<?php

function startsWith($haystack, $needle, $ignoreCase = false) {
    if($ignoreCase) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
    }
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

/**
 * Returns true if $needle is found at the end of the string $haystack
 * 
 * @param string $haystack
 * @param string $needle
 * @return bool
 */
function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function str_replace_last($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function str_contains($contains, $subject, $ignoreCase = false) {
    if (strlen($contains === 0) && strlen($subject) !== 0)
        return false;
    if (strlen($contains === 0) && strlen($subject) === 0)
        return true;
    if ($ignoreCase) {
        $contains = strtolower($contains);
        $subject = strtolower($subject);
    }
    if (@strpos($subject, $contains) !== false) {
        return true;
    }
    return false;
}

function isValidMd5($md5 = '') {
    return preg_match('/^[a-f0-9]{32}$/', $md5);
}

function generateSalt() {
    $string = '';
    for ($i = 0; $i < 5; $i++) {
        $string .= rand($i - rand(0, $i), rand($i, $i * $i));
    }
    $string = sha256($string);
    return $string;
}

function sha256($data) {
    return hash('sha256', $data);
}

function getServerAddress() {
    if (array_key_exists('SERVER_ADDR', $_SERVER))
        return $_SERVER['SERVER_ADDR'];
    elseif (array_key_exists('LOCAL_ADDR', $_SERVER))
        return $_SERVER['LOCAL_ADDR'];
    elseif (array_key_exists('SERVER_NAME', $_SERVER))
        return gethostbyname($_SERVER['SERVER_NAME']);
    else {
        // Running CLI
        if (stristr(PHP_OS, 'WIN')) {
            return gethostbyname(php_uname("n"));
        } else {
            $ifconfig = shell_exec('/sbin/ifconfig eth0');
            preg_match('/addr:([\d\.]+)/', $ifconfig, $match);
            return $match[1];
        }
    }
}

function getSuperClassesOf($parent) {
    $result = array();
    foreach (get_declared_classes() as $class) {
        if (is_subclass_of($class, $parent))
            $result[] = $class;
    }
    return $result;
}

function getSubclassesOf($parent) {
    $result = array();
    foreach (get_declared_classes() as $class) {
        if (is_subclass_of($parent, $class))
            $result[] = $class;
    }
    return $result;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    if($datetime instanceof DateTime) {
        $ago = $datetime;
    } else {
        $ago = new DateTime($datetime);
    }
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function resize_image($file, $w, $h, $crop=FALSE) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    return $dst;
}

function resize_jpg($src, $width, $height, $w, $h, $crop=FALSE) {
    $r = $width / $height;
    $sx = 0;
    $sy = 0;
    $tx = 0;
    $ty = 0;
    
    if ($crop) {
        if ($w/$h > $r) {
            $new_height = $w/$r;
            $new_width = $w;
        } else {
            $new_width = $h*$r;
            $new_height = $h;
        }

        $x_mid = $new_width/2;  //horizontal middle
        $y_mid = $new_height/2; //vertical middle

        $process = imagecreatetruecolor(round($new_width), round($new_height));

        imagecopyresampled($process, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        $thumb = imagecreatetruecolor($w, $h);
        imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($w/2)), ($y_mid-($h/2)), $w, 
                $h, $w, $h);

        imagedestroy($process);
        return $thumb;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, $tx, $ty, $sx, $sy, $newwidth, $newheight, $width, $height);

    return $dst;
}