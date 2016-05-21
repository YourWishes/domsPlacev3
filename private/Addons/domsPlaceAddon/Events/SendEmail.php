<?php
namespace domsPlaceAddon;
if (!defined('MAIN_INCLUDED')) throw new Exception();

import('Ajax.AjaxRequest');
import('Ajax.AjaxRequestHandler');
import('Email.Email');

class SendEmail extends \AjaxRequestHandler {
    private static $INSTANCE;
    
    /**
     * 
     * @return SendEmail
     */
    public static function getInstance() {
        return static::$INSTANCE;
    }
    
    public function __construct() {
        parent::__construct('sendEmail');
        
        //As with most AJAX Request listeners, this self registers
        $this->register();
        static::$INSTANCE = $this;
    }
    
    /**
     * 
     * @param \AjaxRequest $request
     */
    public function onRequest(&$request) {
        parent::onRequest($request);
        
        $data = $request->getData();
        
        if(!isset($data["name"])) $request->send('Missing name.');
        if(!isset($data["email"])) $request->send('Missing email.');
        if(!isset($data["message"])) $request->send('Missing message.');
        
        $name = $data["name"];
        $email_add = $data["email"];
        $message = $data["message"];
        
        $table = \domsPlaceAddon\domsPlaceAddon::getEnquiryEmailsTable();
        
        if(strlen($name) > $table->getField('name')->max_length) $request->send('Too much data.');
        if(strlen(str_replace(' ', '', $name)) == 0) $request->send('Missing name.');
        
        if(!\Email::isValidEmail($email_add)) $request->send('Invalid email.');
        
        if(strlen(str_replace(' ', '', $message)) == 0) $request->send('Missing message.');
        
        $data = new \HashMap('VirtualField');
        $data->putVal($table->getField('name'), $name);
        $data->putVal($table->getField('email'), $email_add);
        $data->putVal($table->getField('message'), $message);
        
        $ventry = $table->createEntry($data);
        try {
            $table->database->commitChanges();
        } catch(\Exception $e) {
            $request->send("Failed to send! Please try again later.");
        }
        
        //First, send one to the server admin (me)
        $email = new \Email();
        $email->addRecipient(getconf('DEFAULT_MAILRECIEVER'));
        $email->setFromAddress($email_add);
        
        $email->setSubject('Online Contact Enquiry Recieved.');
        $email->echoData('<h1>Online Contact Enquiry Recieved</h1>');
        $email->echoData('<table>');
        $email->echoData('<tr>');
        $email->echoData('<td>Name</td><td>'.$email->getTemplate()->escapeHTML($name) . '</td>');
        $email->echoData('</tr>');
        $email->echoData('<tr>');
        $email->echoData('<td>Email</td><td>'.$email->getTemplate()->escapeHTML($email_add) . '</td>');
        $email->echoData('</tr>');
        $email->echoData('<tr>');
        $email->echoData('<td>Message</td><td>'.$email->getTemplate()->escapeHTML($message) . '</td>');
        $email->echoData('</tr>');
        $email->echoData('<table>');
        $email->echoData('<small>Ticket #' . $ventry->getData()->getComp($table->getField('id')) . '</small>');
        
        try {
            $email->send();
        } catch(\Exception $e) {
            $request->send("Failed to send! Please try again later.");
        }
        
        $email = new \Email();
        $email->addRecipient($email_add);
        $email->setFromAddress(getconf('DEFAULT_MAILSENDER'));
        $email->setSubject('Contact Enquiry Sent');
        $email->echoData('<h1>Hello ' . $email->getTemplate()->escapeHTML($name). '</h1>');
        $email->echoData('<p>Your contact enquiry has been sent to me, please allow a few days ');
        $email->echoData('for me to respond to this message, depending on how busy I am. If you need to ');
        $email->echoData('send another message between now and me replying, please revisit the Contact Page, as ');
        $email->echoData('you cannot reply to this email.</p>');
        $email->echoData('<p>If you did not send any contact enquiry, please disregard this email.</p>');
        
        try {
            $email->send();
        } catch(\Exception $e) {
            $request->send("Failed to send! Please try again later.");
        }
        
        $request->send(true);
    }
}