<?php
/**
 * @file Request.php contains the Request class
 *
 * @author Till Uhlig
 * @date 2013-2014
 */ 
include_once( dirname(__FILE__) . '/Request/CreateRequest.php' );   
include_once( dirname(__FILE__) . '/Request/MultiRequest.php' );   
include_once( dirname(__FILE__) . '/Logger.php' );

/**
 * the Request class offers functions to get results of POST,GET,PUT.DELETE and 
 * custom requests. Additional requests can be routed by using routeRequest().
 */
class Request
{
    /**
     * parse a header string
     * @see http://us3.php.net/manual/de/function.http-parse-headers.php
     *
     * @param string $header a string  
     *
     * @return an assoc array, with header entrys
     */
    public static function http_parse_headers( $header )
    {        
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) ) {
                    if (!is_array($retVal[$match[1]])) {
                        $retVal[$match[1]] = array($retVal[$match[1]]);
                    }
                    $retVal[$match[1]][] = $match[2];
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }

    /**
     * performs a custom request
     *
     * @param string $method the request type (POST, DELETE, PUT, GET, ...) 
     * @param string $target the taget URL
     * @param string $header an array with header informations
     * @param string $content the request content/body
     *
     * @return an array with the request result (status, header, content)
     * - ['headers'] = an array of header informations e.g. ['headers']['Content-Type']
     * - ['content'] = the response content
     * - ['status'] = the status code e.g. 200,201,404,409,...
     */
    public static function custom($method, $target, $header,  $content)
    {
        $begin = microtime(true);
        // creates a custom request
        $ch = Request_CreateRequest::createCustom($method,$target,$header,$content);
        $content = curl_exec($ch);
          
        // get the request result
        $result = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        
        // splits the received header info, to create an entry 
        // in the $result['headers'] for each part of the header
        $result['headers'] = Request::http_parse_headers(substr($content, 0, $header_size));

        // seperates the content part
        $result['content'] = substr($content, $header_size);
        
        // sets the received status code
        $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        ///Logger::Log($target . ' ' . (round((microtime(true) - $begin),2)). 's', LogLevel::DEBUG, dirname(__FILE__) . '../executionTime.log');
        return $result; 
    }
       
     
    /**
     * performs a POST request
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an array with the request result (status, header, content)
     */
    public static function post($target, $header,  $content)
    {
        return Request::custom("POST", $target , $header, $content); 
    }
    
    
    /**
     * performs a GET request
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an array with the request result (status, header, content)
     */
    public static function get($target, $header,  $content)
    {
        return Request::custom("GET", $target, $header, $content); 
    }
    
    
    /**
     * performs a DELETE request
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an array with the request result (status, header, content)
     */
    public static function delete($target, $header,  $content)
    {
        return Request::custom("DELETE", $target, $header, $content); 
    } 
    
    /**
     * performs a PUT request
     *
     * @param $target the taget URL
     * @param $header an array with header informations
     * @param $content the request content/body
     *
     * @return an array with the request result (status, header, content)
     */
    public static function put($target, $header,  $content)
    {
        return Request::custom("PUT", $target, $header, $content); 
    } 

    /**
     * the routeRequest function uses a list of links to find a 
     * relevant component for the request
     *
     * @param $method the request type (POST, DELETE, PUT, GET, ...) 
     * @param $resourceUri the target URI
     * @param $header an array with header informations
     * @param $content the request content/body
     * @param $linkedComponents an array of Link objects
     * @param $prefix the prefix that the request needs (String)
     * @param $linkName the name of the link that is to be used (String)
     *
     * @return an array with the request result (status, header, content)
     */
    public static function routeRequest($method , $resourceUri , $header ,  $content , $linkedComponents , $prefix, $linkName=NULL)
    {
        if (!is_array($linkedComponents)) $linkedComponents = array($linkedComponents);
    
        // get possible links
        $else = array();
        foreach ($linkedComponents as $links){
 
            // if $linkName is set, only use links with correct names
            if ($linkName!=NULL && $linkName!=$links->getName())
                continue;
                
            // determines all supported prefixes    
            $possible = explode(',',$links->getPrefix());
            
            if (in_array($prefix,$possible)){
            
                // create a custom request
                $ch = Request::custom($method,
                                      $links->getAddress().$resourceUri,
                                      $header,
                                      $content);
                                  
                // checks the answered status code             
                if ($ch['status']>=200 && $ch['status']<=299){
                
                    // finished
                   //  Logger::Log("routeRequest prefix search done:".$links->getAddress(),LogLevel::DEBUG);
                    return $ch;
                } elseif ($ch['status'] == 401 || $ch['status'] == 404){
                   // Logger::Log("routeRequest prefix search access denied:".$links->getAddress(),LogLevel::DEBUG);
                    return $ch;
                } //else
                  //  Logger::Log("routeRequest prefix search failed:".$links->getAddress(),LogLevel::DEBUG);
                                     
            } elseif(in_array("",$possible)){
                
                // if the prefix is not used, check if the link also 
                // permits any questions and remember him in the $else list
                array_push($else, $links);
            } 
        }
        
        // if no possible link was found or every possible component 
        // answered with a non "finished" status code, we will ask components
        // who are able to work with every prefix
        foreach ($else as $links)
        {
            // create a custom request
            $ch = Request::custom($method,
                                  $links->getAddress().$resourceUri,
                                  $header,
                                  $content);
                                  
            // checks the answered status code                 
            if ($ch['status']>=200 && $ch['status']<=299){
                // finished
               // Logger::Log("routeRequest blank search done:".$links->getAddress(),LogLevel::DEBUG);
                return $ch;
            } elseif ($ch['status'] == 401 || $ch['status'] == 404) {
              //  Logger::Log("routeRequest blank search access denied:".$links->getAddress(),LogLevel::DEBUG);
                return $ch;
            }// else
              //  Logger::Log("routeRequest blank search failed:".$links->getAddress(),LogLevel::DEBUG);
        }
        
        // no positive response or no operative link
        $ch = array();
        $ch['status'] = 412;
        return $ch;
    }

}   
?>