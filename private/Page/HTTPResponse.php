<?php
if (!defined('MAIN_INCLUDED')) throw new Exception();

class HTTPResponse {
    //Instances Definitions
    public static $CONTINUE_100;
    public static $SWITCHING_PROTOCOLS_101;
    public static $PROCESSING_102;
    
    public static $OK_200;
    public static $CREATED_201;
    public static $ACCEPTED_202;
    public static $NON_AUTHORITATIVE_INFORMATION_203;
    public static $NO_CONTENT_204;
    public static $RESET_CONTENT_205;
    public static $PARTIAL_CONTENT_206;
    public static $MULTI_STATUS_207;
    public static $ALREADY_REPORTED_208;
    public static $IM_USED_226;
    
    public static $MULTIPLE_CHOICES_300;
    public static $MOVED_PERMANENTLY_301;
    public static $FOUND_302;
    public static $SEE_OTHER_303;
    public static $NOT_MODIFIED_304;
    public static $USE_PROXY_305;
    public static $SWITCH_PROXY_306;
    public static $TEMPORARY_REDIRECT_307;
    public static $PERMANENT_REDIRECT_308;
    public static $RESUME_INCOMPLETE_308;
    
    public static $BAD_REQUEST_400;
    public static $UNAUTHORIZED_401;
    public static $PAYMENT_REQUIRED_402;
    public static $FORBIDDEN_403;
    public static $NOT_FOUND_404;
    public static $METHOD_NOT_ALLOWED_405;
    public static $NOT_ACCEPTABLE_406;
    public static $PROXY_AUTHENTICATION_REQUIRED_407;
    public static $REQUEST_TIMEOUT_408;
    public static $CONFLICT_409;
    public static $GONE_410;
    public static $LENGTH_REQUIRED_411;
    public static $PRECONDITION_FAILED_412;
    public static $PAYLOAD_TOO_LARGE_413;
    public static $REQUEST_URI_TOO_LONG_414;
    public static $UNSUPPORTED_MEDIA_TYPE_415;
    public static $REQUESTED_RANGE_NOT_SATISFIABLE_416;
    public static $EXPECTATION_FAILED_417;
    public static $IM_A_TEAPOT_418;
    public static $AUTENTICATION_TIMEOUT_419;
    public static $METHOD_FAILURE_420;
    public static $ENHANCE_YOUR_CALM_420;
    public static $MISDIRECTED_REQUEST_421;
    public static $UNPROCESSABLE_ENTITY_422;
    public static $LOCKED_423;
    public static $FAILED_DEPENDANCY_424;
    public static $UPGRADE_REQUIRED_426;
    public static $PRECONDIITON_REQUIRED_428;
    public static $TOO_MANY_REQUESTS_429;
    public static $REQUEST_HEADER_FIELDS_TOO_LARGE_431;
    public static $LOGIN_TIMEOUT_440;
    public static $NO_RESPONSE_444;
    public static $RETRY_WITH_449;
    public static $BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS_450;
    public static $UNAVAILABLE_FOR_LEGAL_REASONS_451;
    public static $REQUEST_HEADER_TOO_LARGE_494;
    public static $CERT_ERROR_495;
    public static $NO_CERT_496;
    public static $HTTP_TO_HTTPS_497;
    public static $TOKEN_EXPIRED_INVALID_498;
    public static $CLIENT_CLOSED_REQUEST_499;
    public static $TOKEN_REQUIRED_499;
    
    public static $INTERNAL_SERVER_ERROR_500;
    public static $NOT_IMPLEMENTED_501;
    public static $BAD_GATEWAY_502;
    public static $SERVICE_UNAVAILABLE_503;
    public static $GATEWAY_TIMEOUT_504;
    public static $HTTP_VERSION_NOT_SUPPORTED_505;
    public static $VARIANT_ALSO_NEGOTIATES_506;
    public static $INSUFFICIENT_STORAGE_507;
    public static $LOOP_DETECTED_508;
    public static $BANDWIDTH_LIMIT_EXCEEDED_509;
    public static $NOT_EXTENDED_510;
    public static $NETWORK_AUTHENTICATION_REQUIRED_511;
    public static $UNKNOWN_ERROR_520;
    public static $ORIGIN_CONNECTION_TIMEOUT_522;
    public static $NETWORK_READ_TIMEOUT_ERROR_598;
    public static $NETWORK_CONNECT_TIMEOUT_ERROR_599;
    
    //Static Methods
    /**
     * 
     * @return HTTPResponse
     */
    public static function getDefault() {
        return HTTPResponse::$OK_200;
    }
    
    //Instance
    private $name;
    private $code;
    private $httpversion;
    
    public function __construct($name, $code, $httpversion, $x) {
        $this->name = $name;
        $this->code = $code;
        $this->httpversion = $httpversion;
    }
    
    public function getName() {return $this->name;}
    public function getCode() {return $this->code;}
    public function getHTTPVersion() {return $this->httpversion;}
}

//Instances
$http1_0 = 'HTTP/1.0';
$http1_1 = 'HTTP/1.1';
$unknown = $http1_0;
$google = $http1_1;

HTTPResponse::$CONTINUE_100                                 = new HTTPResponse('Continue', 100, $http1_1, '');
HTTPResponse::$SWITCHING_PROTOCOLS_101                      = new HTTPResponse('Switching Protocols', 100, $http1_1, '');
HTTPResponse::$PROCESSING_102                               = new HTTPResponse('Processing', 102, $http1_1, '');

HTTPResponse::$OK_200                                       = new HTTPResponse('OK', 200, $http1_0, '');
HTTPResponse::$CREATED_201                                  = new HTTPResponse('Created', 201, $http1_0, '');
HTTPResponse::$ACCEPTED_202                                 = new HTTPResponse('Accepted', 202, $http1_0, '');
HTTPResponse::$PARTIAL_CONTENT_206                          = new HTTPResponse('Partial Content', 203, $http1_0, '');
HTTPResponse::$NON_AUTHORITATIVE_INFORMATION_203            = new HTTPResponse('Non-Authoritative Information', 203, $http1_1, '');
HTTPResponse::$NO_CONTENT_204                               = new HTTPResponse('No Content', 204, $http1_0, '');
HTTPResponse::$RESET_CONTENT_205                            = new HTTPResponse('Reset Content', 205, $http1_1, '');
HTTPResponse::$PARTIAL_CONTENT_206                          = new HTTPResponse('Partial Content', 206, $http1_1, '');
HTTPResponse::$MULTI_STATUS_207                             = new HTTPResponse('Multi-Status', 207, $unknown, '');
HTTPResponse::$ALREADY_REPORTED_208                         = new HTTPResponse('Already Reported', 208, $unknown, '');
HTTPResponse::$IM_USED_226                                  = new HTTPResponse('I\'m Used', 226, $unknown, '');

HTTPResponse::$MULTIPLE_CHOICES_300                         = new HTTPResponse('Multiple Choices', 300, $http1_1, '');
HTTPResponse::$MOVED_PERMANENTLY_301                        = new HTTPResponse('Moved Permanently', 301, $http1_0, '');
HTTPResponse::$FOUND_302                                    = new HTTPResponse('Found', 302, $http1_0, '');
HTTPResponse::$SEE_OTHER_303                                = new HTTPResponse('See Other', 303, $http1_0, '');
HTTPResponse::$NOT_MODIFIED_304                             = new HTTPResponse('Not Modified', 304, $http1_0, '');
HTTPResponse::$USE_PROXY_305                                = new HTTPResponse('Use Proxy', 305, $http1_1, '');
HTTPResponse::$SWITCH_PROXY_306                             = new HTTPResponse('Switch Proxy', 306, $unknown, '');
HTTPResponse::$TEMPORARY_REDIRECT_307                       = new HTTPResponse('Temporary Redirect', 307, $http1_1, '');
HTTPResponse::$PERMANENT_REDIRECT_308                       = new HTTPResponse('Permanent Redirect', 308, $unknown, '');
HTTPResponse::$RESUME_INCOMPLETE_308                        = new HTTPResponse('Resume Incomplete', 308, $google, '');

HTTPResponse::$BAD_REQUEST_400                              = new HTTPResponse('Bad Request', 400, $http1_0, '');
HTTPResponse::$UNAUTHORIZED_401                             = new HTTPResponse('Unauthorized', 401, $http1_0, '');
HTTPResponse::$PAYMENT_REQUIRED_402                         = new HTTPResponse('Payment Required', 402, $http1_0, '');
HTTPResponse::$FORBIDDEN_403                                = new HTTPResponse('Forbidden', 403, $http1_0, '');
HTTPResponse::$NOT_FOUND_404                                = new HTTPResponse('Not Found', 404, $http1_0, '');
HTTPResponse::$METHOD_NOT_ALLOWED_405                       = new HTTPResponse('Method Not Allowed', 405, $http1_1, '');
HTTPResponse::$NOT_ACCEPTABLE_406                           = new HTTPResponse('Not Acceptable', 406, $http1_1, '');
HTTPResponse::$PROXY_AUTHENTICATION_REQUIRED_407            = new HTTPResponse('Proxy Authentication Required', 407, $http1_1, '');
HTTPResponse::$REQUEST_TIMEOUT_408                          = new HTTPResponse('Request Timeout', 408, $http1_1, '');
HTTPResponse::$CONFLICT_409                                 = new HTTPResponse('Conflict', 409, $http1_1, '');
HTTPResponse::$GONE_410                                     = new HTTPResponse('Gone', 410, $http1_1, '');
HTTPResponse::$LENGTH_REQUIRED_411                          = new HTTPResponse('Length Required', 411, $http1_1, '');
HTTPResponse::$PRECONDITION_FAILED_412                      = new HTTPResponse('Precondition Failed', 412, $http1_1, '');
HTTPResponse::$PAYLOAD_TOO_LARGE_413                        = new HTTPResponse('Payload Too Large', 413, $http1_1, '');
HTTPResponse::$REQUEST_URI_TOO_LONG_414                     = new HTTPResponse('Requst-URI Too Long', 414, $http1_1, '');
HTTPResponse::$UNSUPPORTED_MEDIA_TYPE_415                   = new HTTPResponse('Unsupported Media Type', 415, $http1_1, '');
HTTPResponse::$REQUESTED_RANGE_NOT_SATISFIABLE_416          = new HTTPResponse('Requested Range Not Satisfiable', 416, $http1_1, '');
HTTPResponse::$EXPECTATION_FAILED_417                       = new HTTPResponse('Expectation Failed', 417, $http1_1, '');
HTTPResponse::$IM_A_TEAPOT_418                              = new HTTPResponse('I\'m a Teapot', 418, $unknown, '');
HTTPResponse::$AUTENTICATION_TIMEOUT_419                    = new HTTPResponse('Authentication Timeout', 419, $unknown, '');
HTTPResponse::$METHOD_FAILURE_420                           = new HTTPResponse('Mehtod Failure', 420, $unknown, '');
HTTPResponse::$ENHANCE_YOUR_CALM_420                        = new HTTPResponse('Enhance Your Calm', 420, $unknown, '');
HTTPResponse::$MISDIRECTED_REQUEST_421                      = new HTTPResponse('Misdirected Request', 421, $unknown, '');
HTTPResponse::$UNPROCESSABLE_ENTITY_422                     = new HTTPResponse('Unprocessable Entity', 422, $unknown, '');
HTTPResponse::$LOCKED_423                                   = new HTTPResponse('Locked', 423, $unknown, '');
HTTPResponse::$FAILED_DEPENDANCY_424                        = new HTTPResponse('Failed Dependancy', 424, $unknown, '');
HTTPResponse::$UPGRADE_REQUIRED_426                         = new HTTPResponse('Upgrade Required', 426, $unknown, '');
HTTPResponse::$PRECONDIITON_REQUIRED_428                    = new HTTPResponse('Precondition Required', 428, $unknown, '');
HTTPResponse::$TOO_MANY_REQUESTS_429                        = new HTTPResponse('Too Many Requests', 429, $unknown, '');
HTTPResponse::$REQUEST_HEADER_FIELDS_TOO_LARGE_431          = new HTTPResponse('Request Header Fields Too Large', 431, $unknown, '');
HTTPResponse::$LOGIN_TIMEOUT_440                            = new HTTPResponse('Login Timeout', 440, $unknown, '');
HTTPResponse::$NO_RESPONSE_444                              = new HTTPResponse('No Response', 444, $unknown, '');
HTTPResponse::$RETRY_WITH_449                               = new HTTPResponse('Retry With', 449, $unknown, '');
HTTPResponse::$BLOCKED_BY_WINDOWS_PARENTAL_CONTROLS_450     = new HTTPResponse('Blocked By Windows Parental Controls', 450, $unknown, '');
HTTPResponse::$UNAVAILABLE_FOR_LEGAL_REASONS_451            = new HTTPResponse('Unavailable For Legal Reasons', 451, $unknown, '');
HTTPResponse::$REQUEST_HEADER_TOO_LARGE_494                 = new HTTPResponse('Request Header Too Large', 494, $unknown, '');
HTTPResponse::$CERT_ERROR_495                               = new HTTPResponse('Cert Error', 495, $unknown, '');
HTTPResponse::$NO_CERT_496                                  = new HTTPResponse('No Cert', 496, $unknown, '');
HTTPResponse::$HTTP_TO_HTTPS_497                            = new HTTPResponse('HTTP to HTTPS', 497, $unknown, '');
HTTPResponse::$TOKEN_EXPIRED_INVALID_498                    = new HTTPResponse('Token Expired Invalid', 498, $unknown, '');
HTTPResponse::$CLIENT_CLOSED_REQUEST_499                    = new HTTPResponse('Client Closed Request', 499, $unknown, '');
HTTPResponse::$TOKEN_REQUIRED_499                           = new HTTPResponse('Token Required', 499, $unknown, '');

HTTPResponse::$INTERNAL_SERVER_ERROR_500                    = new HTTPResponse('Internal Server Error', 500, $http1_0, '');
HTTPResponse::$NOT_IMPLEMENTED_501                          = new HTTPResponse('Not Implemented', 501, $http1_0, '');
HTTPResponse::$BAD_GATEWAY_502                              = new HTTPResponse('Bad Gateway', 502, $http1_0, '');
HTTPResponse::$SERVICE_UNAVAILABLE_503                      = new HTTPResponse('Service Unavailable', 503, $http1_0, '');
HTTPResponse::$GATEWAY_TIMEOUT_504                          = new HTTPResponse('Gateway Timeout', 504, $http1_1, '');
HTTPResponse::$HTTP_VERSION_NOT_SUPPORTED_505               = new HTTPResponse('HTTP Version Not Supported', 505, $http1_1, '');
HTTPResponse::$VARIANT_ALSO_NEGOTIATES_506                  = new HTTPResponse('Variant Also Negotiates', 506, $unknown, '');
HTTPResponse::$INSUFFICIENT_STORAGE_507                     = new HTTPResponse('Insufficient Storage', 507, $unknown, '');
HTTPResponse::$LOOP_DETECTED_508                            = new HTTPResponse('Loop Detected', 508, $unknown, '');
HTTPResponse::$BANDWIDTH_LIMIT_EXCEEDED_509                 = new HTTPResponse('Bandwidth Limit Exceeded', 509, $unknown, '');
HTTPResponse::$NOT_EXTENDED_510                             = new HTTPResponse('Not Extended', 510, $unknown, '');
HTTPResponse::$NETWORK_AUTHENTICATION_REQUIRED_511          = new HTTPResponse('Network Authentication Required', 511, $unknown, '');
HTTPResponse::$UNKNOWN_ERROR_520                            = new HTTPResponse('Unknown Error', 520, $unknown, '');
HTTPResponse::$ORIGIN_CONNECTION_TIMEOUT_522                = new HTTPResponse('Origin Connection Timeout', 522, $unknown, '');
HTTPResponse::$NETWORK_READ_TIMEOUT_ERROR_598               = new HTTPResponse('Network Read Timeout Error', 598, $unknown, '');
HTTPResponse::$NETWORK_CONNECT_TIMEOUT_ERROR_599            = new HTTPResponse('Network Connect Timeout Error', 599, $unknown, '');