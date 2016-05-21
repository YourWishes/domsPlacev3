<?php
//SECURITY, this will STOP settings getting out, well unless something has this function.
if (!function_exists('setconf')) throw new Exception();

/*
 * Configuration File for Website
 */
if(startsWith(File::getMainDirectory()->getPath(), 'C:')) {
    setconf('DEBUG_MODE', true);                                                    //Debug mode will show more errors and information, but may also contain sensitive information to leak.
} else {
    setconf('DEBUG_MODE', false);                                                    //Debug mode will show more errors and information, but may also contain sensitive information to leak.
}

setconf('SITE_NAME', 'domsPlace');                                              //Human friendly name for site
setconf('TITLE_PREFIX', '');                                                    //Default Title Prefix
setconf('TITLE_POSTFIX', ' - domsPlace.com');                                   //Default Title Postfix
setconf('SEO_TITLE', 'domsPlace on the Web, Gaming, Videos, Programming, Music, Comics, Anime and more.');
setconf('FACEBOOK_PAGE', '');                                                   //ID of your Facebook page (or leave blank)
setconf('SERVER_TIMEZONE', 'Australia/Brisbane');                               //Refer to http://php.net/manual/en/timezones.php for a list of valid names.
setconf('URL', 'http://domsplace.com');

setconf('TEMPLATE_DEFAULT', 'domsPlace');                                       //Template that you wish to use by default.
setconf('TEMPLATE_EMAIL', 'domsPlaceEmail');

setconf('DEFAULT_CONTENT_TYPE', 'text/html');                                   //Default Web Page Content Type
setconf('DEFAULT_SITE_LANGUAGE', 'English');                                    //Site Default Language
setconf('SITE_POWERED_BY', 'Caffiene');                                         //Replaces the X-Powered-By Header, good for helping slow malicious attacks down.
setconf('SITE_SERVER', 'Potato v1.00');                                         //Set the header for the server name, similar to X-Powered-By
setconf('SITE_CHARSET', 'UTF-8');                                               //Set the character set to use on the site, I recommend leaving this as UTF-8

/*setconf('AC_ALLOW_HEADERS', '*');
setconf('AC_ALLOW_ORIGIN', array('http://www.marketdirect.com.au', 'http://marketdirect.com.au'));
setconf('AC_ALLOW_METHODS', 'POST, GET, HEAD');*/
setconf('DEFAULT_MAILSENDER', 'no-reply@domsplace.com');
setconf('DEFAULT_MAILRECIEVER', 'dominic@domsplace.com');
//setconf('MAILER', 'Lemons');


/*
 * OK I can't stress this enough, honestly.
 * ===== READ ALL OF THE BELOW AAANDDD DO MORE RESEARCH!!!! =====
 *
 * First of all, before you do ANYTHING, make sure that you make this file 
 * locked to everyone, store it elsewhere, whatever it takes...
 * 
 * Secondly, make sure your password is a complex piece of code.
 * Bad Examples:
 *      Password
 *      Cathy123990
 *      swordfish
 * 
 * Good Examples:
 *      W(kzY/z/3J_K:R{G@(^fX%PF]?Zek7B[
 *      [`T)69\mGaX8)Usm[jV-?/*K?{.n"q2J
 *      4wW5#BfVK8)7P.z"eG?Z}}by<s3$@n+-
 * 
 * Rule of thumb;
 * The harder it is for you to guess, the harder it is for hackers to guess.
 * 
 * Third, and equally as important as the others
 * Database security, in general.
 * 
 * Make sure you setup specific schema's/databases for this website, secure it
 * and give that database a unique login, never use the 'root' account, ever.
 * Restricting access to specific IP addresses is also crucial.
 * 
 * I CANNOT STRESS THESE POINTS ENOUGH, It is not my fault if you don't take the
 * security of your website and/or database seriously... I do the best I can but
 * I cannot ensure, 100% that your setup is perfect.
 * 
 * Take all measures to ensure security measures are met, and I will not be held
 * responsible for your lack of security measures.
 */
 
/******* FOR SECUIRTY REASONS I HAVE LEFT OUT ALL THE DATABASE INFORMATION FROM THIS FILE *******/
 
setconf('GITHUB_URL', 'https://github.com/YourWishes');
setconf('STEAM_URL', 'http://steamcommunity.com/id/WishYouDo/');
setconf('LINKEDIN_URL', 'http://www.linkedin.com/in/dominic-masters-86732678');
setconf('BITBUCKET_URL', 'https://bitbucket.org/YouWish/');
setconf('YOUTUBE_URL', 'http://www.youtube.com/c/DominicMasters');
setconf('GOOGLE+_URL', 'https://plus.google.com/u/0/+DominicMasters/');
setconf('TWITTER', 'dominic_masters');