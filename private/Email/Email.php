<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();
import('Page.Page');
import('Template.Template');

class Email extends Page {
    public static function isValidEmail($email) {return filter_var($email, FILTER_VALIDATE_EMAIL);}
    
    //Instance
    private $recipients;
    private $bcc_recipients;
    private $from;
    
    private $replyTo;
    
    public function __construct($askingFile=null) {
        parent::__construct($askingFile);
        if(definedconf('TEMPLATE_EMAIL')) {
            $templ = Template::getSystemTemplate(getconf('TEMPLATE_EMAIL'));
        } else {
            $templ = Template::getSystemTemplate('EmailTemplate');
        }
        $this->setTemplate($templ);
        
        $this->from = getconf('DEFAULT_MAILSENDER');
        $this->recipients = new ArrayList();
        $this->bcc_recipients = new ArrayList();
    }
    
    public function getRecipients() {return $this->recipients->createCopy();}
    
    public function setFromAddress($x) {$this->from = $x;}
    
    public function addRecipient($email, $name=null) {
        if(!Email::isValidEmail($email)) throw new Exception('Email Address Invalid.');
        $eml;
        if($name === null) {
            $eml = $email;
        } else {
            $eml = $name . ' <' . $email . '>';
        }
        $this->recipients->add($eml);
    }
    
    public function addBCCRecipient($email, $name=null) {
        if(!Email::isValidEmail($email)) throw new Exception('Email Address Invalid.');
        $eml;
        if($name === null) {
            $eml = $email;
        } else {
            $eml = $name . ' <' . $email . '>';
        }
        $this->bcc_recipients->add($eml);
    }
    
    public function clearRecipients() {
        $this->recipients = new ArrayList();
    }
    
    //Aliases for "Set Title"
    public function getSubject() {return $this->getTitle();}
    public function setSubject($x) {$this->setTitle($x);}
    
    public function send() {
        return $this->endPage();
    }
    
    //Override the default Page action, this is an alias for send()
    public function endPage($data=null) {
        if($data === null) {
            $data = $this->makePage();
        }
        
        $headers = "From: " . $this->from . CRLF;
        if(isset($this->replyTo)) {
            $headers .= "Reply-To: " . $this->replyTo;
        } else {
            $headers .= "Reply-To: " . $this->from;
        }
        $headers .= CRLF;
        
        if(definedconf('MAILER')) {
            $headers .= 'X-Mailer: ' . getconf('MAILER') . CRLF;
        } else {
            $headers .= 'X-Mailer: PHP/' . phpversion() . CRLF;
        }
        
        if($this->bcc_recipients->size() > 0) {
            $headers .= 'Bcc: ' . $this->bcc_recipients->implode() . CRLF;
        }
        
        $headers .= 'MIME-Version: 1.0' . CRLF;
        $headers .= 'Content-type: '.$this->getContentType()->getMIMEType().'; charset=' . $this->getCharset()->getName() . CRLF;
        
        //Strip the "PHP" mailer header
        $oldphpself = $_SERVER['PHP_SELF'];
        $oldremoteaddr = $_SERVER['REMOTE_ADDR'];
        
        $_SERVER['PHP_SELF'] = "/";
        $_SERVER['REMOTE_ADDR'] = getServerAddress();

        //Send the email
        //Final Recipients
        $recips = $this->getRecipients();
        
        if(!mail($recips->implode(), $this->getSubject(), $data, $headers)) {
            $res = false;
        }
        $res = true;
        
        //Restore obfuscated server variables
        $_SERVER['PHP_SELF'] = $oldphpself;
        $_SERVER['REMOTE_ADDR'] = $oldremoteaddr;
        return $res;
    }
}
