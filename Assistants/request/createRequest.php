<?php
/**
 * @file (filename)
 * (description)
 */ 


/**
 * (description)
 */
class createRequest
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
    public static function createGet($method, $target, $header,  $content){
        return createRequest::createCustom("GET", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createPost($method, $target, $header,  $content){
        return createRequest::createCustom("POST", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createPut($method, $target, $header,  $content){
        return createRequest::createCustom("PUT", $target, $header,  $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function createDelete($method, $target, $header,  $content){
        return createRequest::createCustom("DELETE", $target, $header,  $content); 
    }
}
?>