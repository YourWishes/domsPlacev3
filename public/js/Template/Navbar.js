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

login_form = $("#nav-login-form");
login_username_input = login_form.find('*[name="LoginUsername"]');
login_password_input = login_form.find('*[name="LoginPassword"]');
register_username_input = $("#inp-username");
register_password_input = $("#inp-password");
login_btn = $("#btn-login");
register_btn = $("#btn-register");

logout_btn = $("#btn-logout");

/*** Responses ***/
form_responses["submitRegistration"] = function(d) {
    if(is_string(d)) {
        alert(d);
    } else {
        alert("Thank you for registering!");
        refresh();
    }
}

/** Event Handlers **/
login_btn.click(function(e) {
    e.preventDefault();
    var $self = $(this);
    var $btn_inner = $self.children('.btn-inner');
    
    if(login_form.hasClass("open")) {
        login_form.removeClass("open");
        login_form.transition({width: '0px'}, 500, 'in-out');
        $btn_inner.html($self.attr('data-original'));
        $self.removeClass("btn-danger");
    } else {
        login_form.addClass("open");
        login_form.transition({width: 'auto'}, 500, 'in-out');
        $self.attr('data-original', $btn_inner.html());
        $btn_inner.html(generateIcon('power-off'));
        $self.addClass("btn-danger");
        //$self.remove();
    }
});

login_username_input.on('keypress keydown keyup', function(e) {
    var $self = $(this);
    register_username_input.val($self.val());
});
login_password_input.on('keypress keydown keyup', function(e) {
    var $self = $(this);
    register_password_input.val($self.val());
});
register_username_input.on('keypress keydown keyup', function(e) {
    var $self = $(this);
    login_username_input.val($self.val());
});
register_password_input.on('keypress keydown keyup', function(e) {
    var $self = $(this);
    login_password_input.val($self.val());
});

logout_btn.click(function(e) {
    e.preventDefault();
    logout(function(d) {
        if(d == true) {
            refresh();
        } else {
            alert(d);
        }
    });
});

login_form.submit(function(e) {
    e.preventDefault();
    if(login_form.attr("data-submitted")) return;
    
    $data = {};
    $data.username = login_username_input.val();
    $data.password = login_password_input.val();
    login_form.transition({width: '0px'}, 500, 'in-out');
    
    ajaxRequest("submitLogin", $data, function(d) {
        if(is_string(d)) {
            login_form.attr("data-submitted", "");
            login_form.transition({width: 'auto'}, 500, 'in-out');
            alert(d);
        } else {
            refresh();
        }
    });
    login_form.attr("data-submitted", true);
});