<?php
$currentpage = __FILE__;
//Creates and calls an AJAX request using the API that can be built upon.
require_once('../private/Main.php');//MUST be imported.
import('Ajax.AjaxRequest');

//Check if this is a valid JSON/JSONP request.
$jsonp = false;
$jsonp_callback = time();
if(isset($_GET['callback'])) {
    //MOST likely a JSONP request... but confirm
    if(AjaxRequest::isValidJSONPCallback($_GET['callback'])) {
        $jsonp = true;
        $jsonp_callback = $_GET['callback'];
    }
}

//Now we can get our data and parse
$request = '';
$data = array();//Our Data to be passed to the function.
if(isset($_GET['request'])) {
    //Not Ideal, but they are passing the request through GET variables (probably a result of JSONP)
    $request = $_GET['request'];
    
} else if(isset($_POST['request'])) {
    $request = $_POST['request'];
}

if($request === '') {
    throw new Exception('Invalid Request');
}

//Now we can get our actual data, be sure to not add the request.)
foreach($_GET as $k => $l) {
    if($k == 'request') continue;
    $data[$k] = $l;
}

foreach($_POST as $k => $l) {
    if($k == 'request') continue;
    if(isset($data[$k])) continue;
    $data[$k] = $l;
}

//Data set, let's us now please to be making a request event now.
$request = new AjaxRequest($request, $data, $jsonp, $jsonp_callback);
$request->fire();

$request->send('No Response.');
//In theory our work here is done.