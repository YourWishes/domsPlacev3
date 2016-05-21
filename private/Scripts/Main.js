/* 
 * Copyright 2016 Dominic Masters <dominic@domsplace.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

function isUndefined($var) {
    return typeof $var === typeof undefined;
}

function is_string($var) {
    return typeof $var === 'string' || $var instanceof String
}

function getUndefined() {
    return typeof undefined;
}

if(isUndefined(Array.prototype.contains)) {
    Array.prototype.contains = function(obj) {
        return this.indexOf(obj) !== -1;
    };
}
if(isUndefined(Array.prototype.isKeySet)) {
    Array.prototype.isKeySet = function(obj) {
        return obj in this;
    };
}

var imported_classes = [];

function require(url, callback) {
    if(imported_classes.contains(url)) {
        return;
    }
    
    console.log("Importing " + url);
    
    //Now fix url
    $split = url.split('.');
    
    $file = '';
    for(var i = 0; i < $split.length; i++) {
        $file += $split[i];
        if(i < ($split.length-1)) $file += '/';
    }
    $url = DOC_ROOT+"/js/" + $file + ".js";
    
    
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = $url;
    //script.async = true;
    
    if(!isUndefined(callback)) {
        script.onreadystatechange = callback;
        script.onload = callback;
    }

    // Fire the loading
    head.appendChild(script);
    
    imported_classes.push($url);
}

function refresh() {
    document.location.reload(true);
}

function navigate($url) {
    document.location = DOC_ROOT + $url;
}

function ajaxRequest($request, $data, $callback, $callback_error) {
    if(isUndefined($data) || $data === null || $data == null || (typeof $data !== 'object' && !($data instanceof FormData)) ) $data = {};
    if(isUndefined($callback)) $callback = function() {};
    if(isUndefined($callback_error)) $callback_error = function() {};
    
    
    if($data instanceof FormData) {
        $data.append("request", $request);
    
        $.ajax({
            url: DOC_ROOT+"/API",
            data: $data,
            processData: false,
            contentType: false,
            success: $callback,
            error: $callback_error,
            method: "POST"
        });
    } else {
        $data.request = $request;
    
        $.ajax({
            url: DOC_ROOT+"/API",
            data: $data,
            success: $callback,
            error: $callback_error,
            method: "POST"
        });
    }
}