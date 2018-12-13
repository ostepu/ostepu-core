<?php
/**
 * @file Request.php contains the Request class
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2016
 */

include_once ( dirname( __FILE__ ) . '/Structures.php' );
include_once( dirname(__FILE__) . '/CConfig.php' );
include_once( dirname(__FILE__) . '/Request/CreateRequest.php' );
include_once( dirname(__FILE__) . '/Request/MultiRequest.php' );
include_once( dirname(__FILE__) . '/Logger.php' );
include_once( dirname(__FILE__) . '/QEPGenerator.php' );

/**
 * the Request class offers functions to get results of POST,GET,PUT.DELETE and
 * custom requests. Additional requests can be routed by using routeRequest().
 */
class Request
{
    public static $enableLocalCalls = true;
    
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
                //$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                $match[1] = preg_replace_callback(
                    '/(?<=^|[\x09\x20\x2D])./',
                    function ($treffer) {
                      return strtoupper($treffer[0]);
                    },
                    strtolower(trim($match[1]))
                );
                
                if( isset($retVal[$match[1]]) ) {
                    if (!is_array($retVal[$match[1]])) {
                        $retVal[$match[1]] = array($retVal[$match[1]]);
                    }
                    $retVal[$match[1]] = $match[2];
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }

    public static function http_parse_headers_short( $fields )
    {
        $retVal = array();
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                ///$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                $match[1] = preg_replace_callback(
                    '/(?<=^|[\x09\x20\x2D])./',
                    function ($treffer) {
                      return strtoupper($treffer[0]);
                    },
                    strtolower(trim($match[1]))
                );
  
                if( isset($retVal[$match[1]]) ) {
                    if (!is_array($retVal[$match[1]])) {
                        $retVal[$match[1]] = array($retVal[$match[1]]);
                    }
                    $retVal[$match[1]] = $match[2];
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }

    public static $components = null;
    public static $configData = null;
    
    public static function normalizeURL($target)
    {
        $confFile = dirname(__FILE__).'/config.ini';
        if (self::$configData === null && file_exists($confFile)){
            self::$configData = parse_ini_file($confFile,TRUE);
        }
        
        if (self::$configData === null){
            return $target;
        } elseif (isset(self::$configData['PL']['urlExtern']) && isset(self::$configData['PL']['url'])){
            if (strpos($target,self::$configData['PL']['urlExtern'].'/')===0){
                // es wurde eine globale URL erkannt, welche zu einer lokalen umgewandelt werden kann
                return self::$configData['PL']['url'].substr($target,strlen(self::$configData['PL']['urlExtern']));
            }
        }
        return $target;
    }
    
    public static function download($tempTarget, $target, $responseHeader=true, $header=array())
    {   
        $target = self::normalizeURL($target);
        
        $result = array();
        try{
            $fp = fopen($tempTarget.'_2', 'w+');
            
            if ($fp === false){
                $result = array();
                $result['status'] = 408;
                $result['content'] = '';
                $result['headers'] = array();
                return;
            }
            
            $ch = curl_init($target);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            
            if ($responseHeader){
                curl_setopt($ch, CURLOPT_HEADER, true);
            }
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            //curl_setopt($ch, CURLOPT_USERAGENT,'Codular Sample cURL Request');
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 120 Sekunden
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // 120 Sekunden
            
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            $content = curl_exec($ch);
            // get the request result
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            
            if ($header_size > 0){
                fseek($fp,0);
                $result['headers'] = self::http_parse_headers(fread($fp, $header_size));
            }
            fseek($fp,$header_size);
            $fp2 = fopen($tempTarget, 'w+');
            if ($fp2 === false){
                $result = array();
                $result['status'] = 408;
                $result['content'] = '';
                $result['headers'] = array();
                return;
            }
            
            while (!feof($fp)) fwrite($fp2, fread($fp, 8192));
            fclose($fp2);
            fclose($fp);
            unlink($tempTarget.'_2');

            // seperates the content part
            $result['content'] = "";

            // sets the received status code
            $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
        }catch(Exception $e){
            $result = array();
            $result['status'] = 408;
            $result['content'] = '';
            $result['headers'] = array();
        }
        return $result;
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
    public static function custom($method, $target, $header,  $content, $authbool=true, $sessiondelete = false)
    {
        //Logger::Log("$method $target", LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CALL', true, LogLevel::DEBUG);
        $begin = microtime(true);

        $confFile = dirname(__FILE__).'/config.ini';
        if (self::$configData === null && file_exists($confFile)){
            self::$configData =  parse_ini_file($confFile,TRUE);
        }

        // nun soll eine globale URL erkannt werden und in eine lokale überführt werden (wenn möglich)
        if (isset(self::$configData['PL']['urlExtern']) && isset(self::$configData['PL']['url'])){
            if (strpos($target,self::$configData['PL']['urlExtern'].'/')===0){
                // es wurde eine globale URL erkannt, welche zu einer lokalen umgewandelt werden kann
                $target = self::$configData['PL']['url'].substr($target,strlen(self::$configData['PL']['urlExtern']));
            }
        }

        $done = false;
        if (self::$enableLocalCalls && !CConfig::$onload && isset(self::$configData['PL']['url']) && strpos($target,self::$configData['PL']['url'].'/')===0 && file_exists(dirname(__FILE__) . '/request_cconfig.json')){
            if (self::$components===null){
                self::$components=CConfig::loadStaticConfig('','',dirname(__FILE__),'request_cconfig.json');
            }

            $coms = self::$components->getLinks();
            if ($coms!=null){
                if (!is_array($coms)) $coms = array($coms);

                foreach ($coms as $com){
                    if ($com->getPrefix() === null || $com->getLocalPath()==null || $com->getClassFile()==null || $com->getClassName()==null) {
                        ////Logger::Log('nodata: '.$method.' '.$target, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                        continue;
                    }

                    // die Länge des Serverpfades Bsp.: 15
                    $e = strlen(rtrim($_SERVER['DOCUMENT_ROOT'],'/'));

                    $f = substr(str_replace("\\","/",dirname(__FILE__)),$e);
                    $g = substr(str_replace("\\","/",$_SERVER['SCRIPT_FILENAME']),$e);

                    $a=0;
                    for (;$a<strlen($g) && $a<strlen($f) && $f[$a] == $g[$a];$a++){}

                    // der Unterordner. Bsp.: uebungsplattform/
                    $h = substr(str_replace("\\","/",$_SERVER['PHP_SELF']),0,$a-1);

                    // ermittelt den Anfang der lokalen URL (ohne Unterordner). Bsp.: http://localhost
                    $basePath = substr(self::$configData['PL']['url'], 0,strlen(self::$configData['PL']['url'])-strlen($h));

                    $url = self::$configData['PL']['url'].'/'.$com->getLocalPath();

                    if (strpos($target,$url.'/')===0){
                        $result = array();
                        $tar = dirname(__FILE__).'/../'.$com->getLocalPath().'/'.$com->getClassFile();
                        $tar=str_replace("\\","/",$tar);
                        if (!file_exists($tar)) continue;
                        $add = substr($target,strlen($url));

                        $sid = QEPGenerator::getNextSid();
                        QEPGenerator::setCacheSid($sid);
                        QEPGenerator::getTree($target, $method);
                        //Logger::Log("call me: $sid, $method $target", LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);

                        $targetBeginTime = microtime(true);
                        //////$cachedData = QEPGenerator::getCachedDataByURL($target, $method);
                        //Logger::Log("++".$method.' '.$target, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);


                        if (isset($cacheData) && $cachedData!==null){
                            $result['content'] = $cachedData->content;
                            $result['status'] = $cachedData->status;
                            ///Logger::Log('out>> '.$method.' '.$target, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');
                            //////QEPGenerator::cacheData($sid, $com->getTargetName(), $target, $result['content'], $result['status'], $method);
                        } else {
                            $args = array(
                                          'REQUEST_METHOD' => $method,
                                          'PATH_INFO' => $add,
                                          'slim.input' => $content);

                            if (isset($_SERVER['HTTP_SESSION']))
                                $args['HTTP_SESSION'] = $_SERVER['HTTP_SESSION'];
                            if (isset($_SERVER['HTTP_USER']))
                                $args['HTTP_USER'] = $_SERVER['HTTP_USER'];

                            if ($authbool){
                                if (isset($_SESSION['UID'])){
                                    $args['HTTP_USER'] = $_SESSION['UID'];
                                    $_SERVER['HTTP_USER'] = $_SESSION['UID'];
                                }
                                if (isset($_SESSION['SESSION'])){
                                    $args['HTTP_SESSION'] = $_SESSION['SESSION'];
                                    $_SERVER['HTTP_SESSION'] = $_SESSION['SESSION'];
                                }

                                if ($sessiondelete) {
                                    if (isset($_SERVER['REQUEST_TIME'])){
                                        $args['HTTP_DATE'] = $_SERVER['REQUEST_TIME'];
                                        $_SERVER['HTTP_DATE'] = $_SERVER['REQUEST_TIME'];
                                    }
                                } else {
                                    if (isset($_SESSION['LASTACTIVE'])){
                                        $args['HTTP_DATE'] = $_SESSION['LASTACTIVE'];
                                        $_SERVER['HTTP_DATE'] = $_SESSION['LASTACTIVE'];
                                    }
                                }
                            }

                            if (isset($_SERVER['HTTP_DATE']))
                                $args['HTTP_DATE'] = $_SERVER['HTTP_DATE'];

                            $oldArgs = array('REQUEST_METHOD' => \Slim\Environment::getInstance()->offsetGet('REQUEST_METHOD'),
                                             'PATH_INFO' => \Slim\Environment::getInstance()->offsetGet('PATH_INFO'),
                                             'slim.input' => \Slim\Environment::getInstance()->offsetGet('slim.input'),
                                             'HTTP_DATE' => \Slim\Environment::getInstance()->offsetGet('HTTP_DATE'),
                                             'HTTP_USER' => \Slim\Environment::getInstance()->offsetGet('HTTP_USER'),
                                             'HTTP_SESSION' => \Slim\Environment::getInstance()->offsetGet('HTTP_SESSION'),
                                             'REQUEST_TIME' => \Slim\Environment::getInstance()->offsetGet('REQUEST_TIME'));

                            $oldRequestURI = $_SERVER['REQUEST_URI'];
                            $oldScriptName = $_SERVER['SCRIPT_NAME'];
                            ///$oldRedirectURL = $_SERVER['REDIRECT_URL'];
                            $_SERVER['REQUEST_URI'] = substr($target,strlen($basePath.'/')-1);
                            $_SERVER['SCRIPT_NAME'] = $h.'/'.$com->getLocalPath().'/'.$com->getClassFile();
                            $_SERVER['QUERY_STRING'] = '';
                            $_SERVER['REQUEST_METHOD'] = $method;
                            \Slim\Environment::mock($args);
                            include_once($tar);

                            $oldStatus = http_response_code();
                            $oldHeader = array_merge(array(),headers_list());
                            header_remove();
                            http_response_code(0);

                            $name = $com->getClassName();
                            
                            try {
                                ob_start();

                                $newSid = QEPGenerator::getNextSid();
                                QEPGenerator::setCacheSid($newSid);
                                QEPGenerator::createNode($newSid, $com->getTargetName(), $method, $target, $content);
                                ////Logger::Log('newSid: '.$newSid, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);

                                // merkt sich das alte Arbeitsverzeichnis
                                $pathOld = getcwd();
                                @chdir(dirname($tar));
                                $obj = new $name();
                                if (isset($obj))
                                    unset($obj);

                                // setzt das Arbeitsverzeichnis auf den Pfad zurück, den es vor
                                // dem Aufruf hatte
                                @chdir($pathOld);
                            
                                $result['content'] = ob_get_contents();
                                QEPGenerator::setETag($result['content']);
                                $result['headers'] = array_merge(array(),Request::http_parse_headers_short(headers_list()));
                                header_remove();
                                
                                //if (!isset($result['headers']['Cachesid'])){
                                    //$newSid = QEPGenerator::getNextSid();
                                    $result['headers']['Cachesid'] = $newSid;
                                   // Logger::Log('newSid: '.$newSid, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);

                                    //QEPGenerator::setCacheSid($newSid);
                                //}
                                //$result['headers']['Cachesid'] = $newSid;
                                ob_end_clean();
                                //header_remove();

                                $result['status'] = http_response_code();
                            } catch(Exception $e) {
                                error_log($e->getMessage());
                                header_remove();
                                $result['status'] = '500';
                                $result['content'] = '';
                                $result['headers'] = array();
                            }
        
                            $_SERVER['REQUEST_URI'] = $oldRequestURI;
                            $_SERVER['SCRIPT_NAME'] = $oldScriptName;
                            //$_SERVER['REDIRECT_URL'] = $oldRedirectURL;

                            \Slim\Environment::mock($oldArgs);
                            $_SERVER['REQUEST_METHOD'] = $oldArgs['REQUEST_METHOD'];
                            http_response_code($oldStatus);

                            header('Content-Type: text/html');
                            foreach ($oldHeader as $head) {
                                header($head);
                            }

                            $targetSid = (isset($result['headers']['Cachesid']) ? $result['headers']['Cachesid'] : null);
                            QEPGenerator::releaseNode($targetSid, $result['content'], $result['status'], $com->getLocalPath(), (isset($result['headers']['Content-Type']) ? $result['headers']['Content-Type'] : null));
                            //////QEPGenerator::addPath($sid, $targetSid, $com->getTargetName(), $target, $method, $result['status']);
                            //////QEPGenerator::finishRequest($targetSid, $h.'/'.$com->getLocalPath(), $com->getTargetName(), $target, $result['content'], $result['status'], $method, $content);
                            //////QEPGenerator::cacheData($sid, $com->getTargetName(), $target, $result['content'], $result['status'], $method);
                            ///Logger::Log('in<< '.$method.' '.$com->getClassName().$add, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log');

                        }

                        $done=true;
                        break;
                    }
                }
            }
        }
        
        if (!$done){
            // creates a custom request

            ////Logger::Log("--".$method.' '.$target, LogLevel::DEBUG, false, dirname(__FILE__) . '/../calls.log', 'CACHE', true, LogLevel::DEBUG);

            
            try{
                $ch = Request_CreateRequest::createCustom($method,$target,$header,$content, $authbool, $sessiondelete)->get();
                $content = curl_exec($ch);
                // get the request result
                $result = curl_getinfo($ch);
                $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

                // splits the received header info, to create an entry
                // in the $result['headers'] for each part of the header
                $result['headers'] = self::http_parse_headers(substr($content, 0, $header_size));

                // seperates the content part
                $result['content'] = substr($content, $header_size);

                // sets the received status code
                $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
            }catch(Exception $e){
                $result = array();
                $result['status'] = 408;
                $result['content'] = '';
                $result['headers'] = array();
            }
        }

        ////Logger::Log($target . ' ' . (round((microtime(true) - $begin),2)). 's', LogLevel::DEBUG, false, dirname(__FILE__) . '/../executionTime.log');
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
    public static function post($target, $header,  $content, $authbool=true, $sessiondelete = false)
    {
        return self::custom("POST", $target , $header, $content, $authbool, $sessiondelete);
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
    public static function get($target, $header,  $content, $authbool=true, $sessiondelete = false)
    {
        return self::custom("GET", $target, $header, $content, $authbool, $sessiondelete);
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
    public static function delete($target, $header,  $content, $authbool=true, $sessiondelete = false)
    {
        return self::custom("DELETE", $target, $header, $content, $authbool, $sessiondelete);
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
    public static function put($target, $header,  $content, $authbool=true, $sessiondelete = false)
    {
        return self::custom("PUT", $target, $header, $content, $authbool, $sessiondelete);
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
    public static function routeRequest($method , $resourceUri , $header ,  $content , $linkedComponents , $prefix=null, $linkName=NULL)
    {
        if (!is_array($linkedComponents)) $linkedComponents = array($linkedComponents);

        // get possible links
        $else = array();
        if ($linkedComponents == null || (count($linkedComponents) == 1 && $linkedComponents[0] == null)){
            // there aren't any links
            $ch = array();
            $ch['status'] = 404;
            return $ch;
        }

        foreach ($linkedComponents as $links){
            if ($links==null)
                continue;

            // if $linkName is set, only use links with correct names
            if ($linkName!=NULL && $linkName!=$links->getName())
                continue;

            // determines all supported prefixes
            $possible = explode(',',$links->getPrefix());

            if ($prefix === null || in_array($prefix,$possible)){

                // create a custom request
                $ch = self::custom($method,
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
            $ch = self::custom($method,
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
