<?php
/**
 * @file CreateRequest.php contains the Request_CreateRequest class
 */ 


/**
 * The Request_CreateRequest class is used to create and 
 * initialize an request objects
 *
 * @author Till Uhlig
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
    public static function createCustom($method, $target, $header,  $content){
        $ch = curl_init($target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        return $ch; 
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
    public static function createGet($target, $header,  $content){
        return Request_CreateRequest::createCustom("GET", 
                                                    $target, 
                                                    $header,  
                                                    $content); 
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
    public static function createPost($target, $header,  $content){
        return Request_CreateRequest::createCustom("POST", 
                                                    $target, 
                                                    $header,  
                                                    $content); 
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
    public static function createPut($target, $header,  $content){
        return Request_CreateRequest::createCustom("PUT", 
                                                    $target, 
                                                    $header,  
                                                    $content); 
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
    public static function createDelete($target, $header,  $content){
        return Request_CreateRequest::createCustom("DELETE", 
                                                    $target, 
                                                    $header,  
                                                    $content); 
    }
}
?>