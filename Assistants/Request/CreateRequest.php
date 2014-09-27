<?php
/**
 * @file CreateRequest.php contains the Request_CreateRequest class
 */ 


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
        $ch = curl_init($target);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // take the SESSION, DATE and USER fields from received header and 
        // add them to the header of our curl object
        $resultHeader = array();
                
        if ($authbool){
            if (isset($_SESSION['UID']))
                $resultHeader['USER'] = 'USER: ' . $_SESSION['UID'];
            if (isset($_SESSION['SESSION']))
                $resultHeader['SESSION'] = 'SESSION: ' . $_SESSION['SESSION'];
                                                
            if ($sessiondelete) {
                if (isset($_SERVER['REQUEST_TIME']))
                    $resultHeader['DATE'] = 'DATE: ' . $_SERVER['REQUEST_TIME'];
            } else {
                if (isset($_SESSION['LASTACTIVE']))
                    $resultHeader['DATE'] = 'DATE: ' . $_SESSION['LASTACTIVE'];
            }
        }
        
        if (isset($_SERVER['HTTP_SESSION']) && !in_array('SESSION',$resultHeader))
            $resultHeader['SESSION'] = 'SESSION: ' . $_SERVER['HTTP_SESSION'];
        if (isset($_SERVER['HTTP_USER']) && !in_array('USER',$resultHeader))
            $resultHeader['USER'] = 'USER: ' . $_SERVER['HTTP_USER'];
        if (isset($_SERVER['HTTP_DATE']) && !in_array('DATE',$resultHeader))
            $resultHeader['DATE'] = 'DATE: ' . $_SERVER['HTTP_DATE'];
            
        $resultHeader = array_values($resultHeader);    
        $resultHeader = array_merge($resultHeader,$header);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $resultHeader);
        
        if ($method == 'POST' || $method == 'PUT'){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }
        
        /**
         * @todo CURLOPT_FRESH_CONNECT and CURLOPT_FORBID_REUSE, we need that?
         */
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 

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
?>