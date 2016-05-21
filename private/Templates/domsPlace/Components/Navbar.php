<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Template.Component.TemplateComponent');
import('Template.Template');
import('Page.Page');

new TemplateComponent('Navbar', function(&$template, &$page, $data) {
    if(!($template instanceof domsPlace)) throw new Exception();
    if(!($page instanceof Page)) throw new Exception();
    //Generate Code Here
    
    //Now the HTML
    $x = '';//Here is our StringBuffer for the navbar code.
        
    $m = '';
    $form = new FormsAddon\Form();
    $username_input = new FormsAddon\FormControl('username', FormsAddon\FormControlType::$TEXT);
    $username_input->label = "Username";
    $username_input->placeholder = "Enter your desired Username";
    $username_input->max_length = \UsersAddon\RegisteredUser::$MAX_USERNAME_LENGTH;
    $username_input->required = true;
    $username_input->title = "Enter a desired username for your profile.";
    
    $password_input = new FormsAddon\FormControl('password', FormsAddon\FormControlType::$PASSWORD);
    $password_input->label = "Password";
    $password_input->placeholder = "Enter your desired Password.";
    $password_input->required = true;
    $password_input->title = "Enter a desired password.";
    
    $email_input = new FormsAddon\FormControl('email', FormsAddon\FormControlType::$EMAIL);
    $email_input->label = "Email";
    $email_input->placeholder = "Enter your email address.";
    $email_input->required = true;
    $email_input->title = "Enter your email address..";
    
    $agree = new FormsAddon\FormControl('terms', FormsAddon\FormControlType::$CHECKBOX);
    $agree->label = "I agree to the website terms.";
    $agree->description = 'By filling out this form, you agree to our <a href="'.File::getTopDirectoryAsHTTP().'/Policy/">Terms and Conditions</a>';
    
    $reg_input = new FormsAddon\FormControl('register', FormsAddon\FormControlType::$SUBMIT);
    $reg_input->value = "Register";
    $form->setAction(\UsersAddon\UserSubmitRegistration::getInstance());
    
    $form->addControl($username_input)->addControl($password_input)->addControl($email_input)->addControl($agree)->addControl($reg_input);
    $m .= $template->getComponent('Form')->generate($template, $page, $form);
    $x .= $template->generateModal('RegisterModal', $m, 'Register');
    
    //Navbar
    $x .= '<nav class="navbar navbar-default">';
    $x .= '<div class="container">';
    $x .= '<div class="row">';

    //Logo
    $x .= '<div class="col-sm-3 logo-container">';
    $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/" class="logo">';
    $x .= '</a>';
    $x .= '</div>';

    //Navbar Buttons
    $x .= '<div class="col-sm-9 nav-links">';
    $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/projects" class="btn btn-default">Projects</a>';
    $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/about" class="btn btn-default">About me</a>';
    
    if(\UsersAddon\User::isLoggedIn()) {
        $x .= '<button class="btn btn-success">' . $template->generateIcon('cog') . '</button>';
        
        if(\PermissionsAddon\Permissions::VIEW_ADMIN_PANEL()->canCurrentUser()) {
            $x .= '<a href="'.File::getTopDirectoryAsHTTP().'/admin/" class="btn btn-warning">' . $template->generateIcon('lock') . '</a>';
        }
        
        $x .= '<button id="btn-logout" class="btn btn-danger">' . $template->generateIcon('power-off') . '</button>';
    } else {
        $x .= '<form '
                . 'id="nav-login-form" '
                . 'onSubmit="return navBarLogin();" '
                . 'action="'.File::getTopDirectoryAsHTTP().'/User/Login/" '
                . 'method="POST" '
                . 'class="login-form">';

        $x .= '<label class="sr-only" for="LoginUsername">Username</label>';
        $x .= $template->generateInput('LoginUsername', 'text', 'Enter your username.', true, null, \UsersAddon\RegisteredUser::$MAX_USERNAME_LENGTH);

        $x .= '<label class="sr-only" for="LoginPassword">Password</label>';
        $x .= $template->generateInput('LoginPassword', 'password', 'Enter your password.', true);
        $x .= '<button type="submit" id="login-btn" class="btn btn-success">'.$template->generateIcon('key').'</button>';
        $x .= '<button type="button" id="register-btn" class="btn btn-default" data-toggle="modal" data-target="#RegisterModal">Register</button>';
        $x .= '</form>';

        $x .= '<button class="btn btn-default" type="button" id="btn-login">';
        $x .= '<div class="btn-inner">'.$template->generateIcon('cog').' Login/Register</div></button>';
        
    }
    $x .= '</div>';
    
    $x .= '</div>';
    $x .= '</div>';
    $x .= '</nav>';

    //End Navbar

    return $x;//Finally return the string buffer
}, function(&$template) {
    //Construct Code Here
    if(!($template instanceof domsPlace)) throw new Exception('Invalid Template.');
    
});