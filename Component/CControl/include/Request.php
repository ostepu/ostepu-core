<?php
/**
 * @file (filename)
 * (description)
 */ 
include 'request/createRequest.php';
include 'request/multiRequest.php';

/**
 * (description)
 */
class Request
{
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function custom($method, $target, $header,  $content){
        $ch = createRequest::createCustom($method,$target,$header,$content);
        $content = curl_exec($ch);
          
        $result = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $result['headers'] = array();
        $head = explode("\r\n",substr($content, 0, $header_size));
                    foreach ($head as $k){
                        $value = split(": ",$k);
                        $result['headers'][$value[0]] = $k;
                    }

        $result['content'] = substr($content, $header_size);
        $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        return $result; 
    }
       
     
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function post($target, $header,  $content){
        return Request::custom("POST", $target , $header, $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function get($target, $header,  $content){
        return Request::custom("GET", $target, $header, $content); 
    }
    
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function delete($target, $header,  $content){
        return Request::custom("DELETE", $target, $header, $content); 
    } 
    
    /**
     * (description)
     *
     * @param $target (description)
     * @param $method (description)
     * @param $content (description)
     */
    public static function put($target, $header,  $content){
        return Request::custom("PUT", $target, $header, $content); 
    } 
}   
?>