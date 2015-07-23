<?php
/**
 * @file CreateRequest.php contains the Request_CreateRequest class
 */ 

include_once( dirname( __FILE__ ) . '/RequestObject.php' );

/**
 * The Request_CreateRequest class is used to create and 
 * initialize an request objects
 *
 * @author Till Uhlig
 * @date 2013-2014
 */
class Request_CreateRequest
{
    /**
     * creates an custom curl request object 
     *
     * @param $method the request type (POST, DELETE, PUT, GET, ...) 
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an curl request object 
     */
    public static function createCustom($method, $target, $header, $content, $authbool = true, $sessiondelete = false)
    {        
        return new Request_RequestObject($method,$target, $header, $content, $authbool, $sessiondelete);
    }
    

    /**
     * creates an GET curl request object 
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an curl request object 
     */
    public static function createGet($target, $header,  $content, $authbool = true, $sessiondelete = false)
    {
        return Request_CreateRequest::createCustom("GET", 
                                                    $target, 
                                                    $header,  
                                                    $content,  
                                                    $authbool,  
                                                    $sessiondelete); 
    }
    
    
    /**
     * creates an POST curl request object 
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an curl request object 
     */
    public static function createPost($target, $header,  $content, $authbool = true, $sessiondelete = false)
    {
        return Request_CreateRequest::createCustom("POST", 
                                                    $target, 
                                                    $header,  
                                                    $content,  
                                                    $authbool,  
                                                    $sessiondelete); 
    }
    
    
    /**
     * creates an PUT curl request object 
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an curl request object 
     */
    public static function createPut($target, $header,  $content, $authbool = true, $sessiondelete = false)
    {
        return Request_CreateRequest::createCustom("PUT", 
                                                    $target, 
                                                    $header,  
                                                    $content,  
                                                    $authbool,  
                                                    $sessiondelete); 
    }
    
    
    /**
     * creates an DELETE curl request object 
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an curl request object 
     */
    public static function createDelete($target, $header,  $content, $authbool = true, $sessiondelete = false)
    {
        return Request_CreateRequest::createCustom("DELETE", 
                                                    $target, 
                                                    $header,  
                                                    $content,  
                                                    $authbool,  
                                                    $sessiondelete); 
    }
}