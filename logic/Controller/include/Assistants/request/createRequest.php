<?php
/**
 * @file (filename)
 * (description)
 */ 


/**
 * (description)
 */
class Request_CreateRequest
{
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createCustom($method, $target, $header,  $content){
        $ch = curl_init($target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        return $ch; 
    }
    

    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createGet($target, $header,  $content){
        return Request_CreateRequest::createCustom("GET", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createPost($target, $header,  $content){
        return Request_CreateRequest::createCustom("POST", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createPut($target, $header,  $content){
        return Request_CreateRequest::createCustom("PUT", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createDelete($target, $header,  $content){
        return Request_CreateRequest::createCustom("DELETE", $target, $header,  $content); 
    }
}
?>