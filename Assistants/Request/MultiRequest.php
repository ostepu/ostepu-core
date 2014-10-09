<?php
/**
 * @file MultiRequest.php contains the Request_MultiRequest class
 */

/**
 * the Request_MultiRequest class is used to work with parallel curl
 * requests.
 *
 * @author Till Uhlig
 * @author Ralf Busch
 * @date 2013-2014
 */
class Request_MultiRequest
{
    /**
     * @var curl_multi $requests a curl multi object
     */
    private $requests;

    /**
     * @var curl[] $handles this list of request is to remember the released
     * requests (used in run())
     */
    private $handles=array();

    private $rolling_window = 4;
    private $i = 0;

    /**
     * the constructor
     */
    public function __construct()
    {
        // initialize a curl multi instance
        //$this->requests = curl_multi_init();
    }

    /**
     * adds an curl request object to $requests list of this instance
     *
     * @param $request a curl request
     */
    public function addRequest($request)
    {
        /*if ($this->i < $this->rolling_window) {
            $this->i++;
            curl_multi_add_handle($this->requests,$request);
        }*/
        $this->handles[] = $request;
    }

    /**
     * this function starts the request process
     *
     * @ return a array of request results
     */
    
    public function run()
    {
        $res = array();
        foreach ($this->handles as $handle){
           $res[] = Request::custom($handle->method, $handle->target, $handle->header,  $handle->content, $handle->authbool, $handle->sessiondelete);
        }
        /*// this var stores the number of running requests
        $running_handles = null;

        $res = array();

        $maxx = count($this->handles);
        for($a = 0; $a<$maxx;$a++)
            $res[] = null;

        do {

            while (($execrun = curl_multi_exec($this->requests, $running_handles)) === CURLM_CALL_MULTI_PERFORM) {
            }
            
            if($execrun != CURLM_OK){
                break;
            }
                
            // a request was just completed -- find out which one
            while(($done = curl_multi_info_read($this->requests))!== false) {
                $info = curl_getinfo($done['handle']);

               // if ($info['http_code'] == 200 || $info['http_code'] == 201 || $info['http_code'] == 404 || $info['http_code'] == 401)  {

                $content  = curl_multi_getcontent($done['handle']);

                $result = curl_getinfo($done['handle']);
                $header_size = curl_getinfo($done['handle'], CURLINFO_HEADER_SIZE);

                $result['headers'] = Request::http_parse_headers(substr($content, 0, $header_size));
                $result['content'] = substr($content, $header_size);
                $result['status'] = curl_getinfo($done['handle'], CURLINFO_HTTP_CODE);

                $res[array_search($done['handle'], $this->handles)] = $result;

                if ($this->i < $maxx){
                    curl_multi_add_handle($this->requests,$this->handles[$this->i]);
                    $this->i++;                    
                }

                    // remove the curl handle that just completed
                    curl_multi_remove_handle($this->requests, $done['handle']);
            }
        
            //Request_MultiRequest::Sleeper(10);
        } while ($running_handles);

        // close the multi curl object
        curl_multi_close($this->requests);*/
        return $res;
    }
    
    // @see http://php.net/manual/de/function.usleep.php#72284
    public static function Sleeper($mSec)
    {
        //    For dummies like me who spent 5 minutes
        //    wondering why socket_create wasn't defined
        if(!function_exists('socket_create')){
            die("Please enable extension php_sockets.dll");
        }

        //    So the socket is only created once
        static $socket=false;
        if($socket===false){
            $socket=array(socket_create(AF_INET,SOCK_RAW,0));
        }
        $pSock=$socket;
       
        //    Calc time
        $uSex = $mSec * 1000;

        //    Do the waiting
        $except=0;$write=NULL;$read=NULL;
        socket_select($read,$write,$pSock,$except,$uSex);
       
        //    OCD
        return true;
    }
}
?>