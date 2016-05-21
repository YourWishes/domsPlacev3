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
form_responses = [];

$(document).ready(function() {
    require('Template.transit');
    require('Template.Navbar');
    
    $(document).ready(function() {
        $('.summernote').each(function() {
            $(this).summernote();
        });
    });
});

function generateIcon($icn, $addClasses) {
    if(isUndefined($addClasses)) $addClasses = [];
    var $x = '<i class="fa fa-' + $icn;
    for($i = 0; $i < $addClasses.length; $i++) {
        $x += ' ' + $addClasses[$i];
    }
    $x += '"></i>';
    return $x;
}

function logout($callback) {
    ajaxRequest('logout', getUndefined(), $callback);
}


function callAjax(request, data) {
    var func = getUndefined();
    
    if(!form_responses.isKeySet(request)) {
        func = function(d) {
            console.log("Recieved Data: ");
            console.log(d);
        };
    } else {
        func = form_responses[request];
    }
    
    ajaxRequest(request, data, func);
}

function ajaxForm(form) {
    var $form = $(form);
    var request = $form.attr("data-request");
    var formData = new FormData(form);
    formData.append("request", request);
    
    var func = getUndefined();
    
    if(!form_responses.isKeySet(request)) {
        func = function(d) {
            console.log("Recieved Data: ");
            console.log(d);
        };
    } else {
        func = form_responses[request];
    }
    
    ajaxRequest(request, formData, func);
    return false;
}