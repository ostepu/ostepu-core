<?php
/**
 * @file MultiRequest.php contains the Request_MultiRequest class
 */ 

/**
 * the Request_MultiRequest class is used to work with parallel curl
 * requests.
 *
 * @author Till Uhlig
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

    private $rolling_window = 3;
    private $i = 0;
    
    /**
     * the constructor
     */ 
    public function __construct()
    {
        // initialize a curl multi instance
        $this->requests = curl_multi_init();
    }
    
    /**
     * adds an curl request object to $requests list of this instance
     *
     * @param $request a curl request
     */ 
    public function addRequest($request)
    {
        if ($this->i < $this->rolling_window) {
            $this->i = $this->i + 1;
            curl_multi_add_handle($this->requests,$request);
        }
        array_push($this->handles,$request);
    }
    
    /**
     * this function starts the request process
     *
     * @ return a array of request results
     */ 
    public function run()
    {
        // this var stores the number of running requests
        $running_handles = null;
        
        // execute all requests and waits for the first incoming 
        // "performing" message
       /* do {
            $status_cme = curl_multi_exec($this->requests, $running_handles);
        } while ($status_cme == CURLM_CALL_MULTI_PERFORM);

        
        while ($running_handles && $status_cme == CURLM_OK) {

            if (curl_multi_select($this->requests) != -1) {
            
                // waits while curl perform the requests
                do {
                    $status_cme = curl_multi_exec($this->requests, 
                                                $running_handles);
                                                
                } while ($status_cme == CURLM_CALL_MULTI_PERFORM);
            } else
            $status_cme = curl_multi_exec($this->requests, $running_handles);
        }*/
        $res = array();
        $maxx = count($this->handles);
        do {
            while(($execrun = curl_multi_exec($this->requests, $running_handles)) == CURLM_CALL_MULTI_PERFORM);
            if($execrun != CURLM_OK)
                break;
            // a request was just completed -- find out which one
            while($done = curl_multi_info_read($this->requests)) {
                $info = curl_getinfo($done['handle']);
                if ($info['http_code'] == 200 || $info['http_code'] == 404)  {
                    

                    $content  = curl_multi_getcontent($done['handle']);

                    $result = curl_getinfo($done['handle']);
                    $header_size = curl_getinfo($done['handle'], CURLINFO_HEADER_SIZE);
                    
                    $result['headers'] = array();
                    $head = explode("\r\n",substr($content, 0, $header_size));
                    foreach ($head as $f){
                        $value = split(": ",$f);
                        if (count($value)>=2){
                            $result['headers'][$value[0]] = $value[1];
                        }
                    }
            
                    $result['content'] = substr($content, $header_size);
                    $result['status'] = curl_getinfo($done['handle'], CURLINFO_HTTP_CODE);

                    $res[array_search($done['handle'], $this->handles)] = $result;
                    //array_push($res,$result);
                    
                    if ($this->i < $maxx)
                    curl_multi_add_handle($this->requests,$this->handles[$this->i]);
                    $this->i = $this->i + 1;

                    // remove the curl handle that just completed
                    curl_multi_remove_handle($this->requests, $done['handle']);
                } else {
                    // request failed.  add error handling.
                }
            }
        } while ($running_handles);
        
        // now we can generate the result data for every request
        
        /*foreach($this->handles as $k){    
        
            $error = curl_error($k);
            
            // check for errors, during execution
            if(!empty($error)){
                $result ='';
                array_push($res,$result);
            } else{
                // successfully executed, so we can create the result
                $content  = curl_multi_getcontent( $k );
                $result = curl_getinfo($k);
                $header_size = curl_getinfo($k, CURLINFO_HEADER_SIZE);
                
                $result['headers'] = array();
                $head = explode("\r\n",substr($content, 0, $header_size));
                foreach ($head as $f){
                    $value = split(": ",$f);
                    if (count($value)>=2){
                        $result['headers'][$value[0]] = $value[1];
                    }
                }
        
                $result['content'] = substr($content, $header_size);
                $result['status'] = curl_getinfo($k, CURLINFO_HTTP_CODE); 
                array_push($res,$result);
            }

            // close current handler
            //curl_multi_remove_handle($this->requests, $k );
        }*/

        // close the multi curl object
        curl_multi_close($this->requests); 
        return $res;
    }    
}
?>