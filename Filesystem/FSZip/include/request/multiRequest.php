<?php
/**
 * @file (filename)
 * (description)
 */ 

/**
 * (description)
 */
class multiRequest
{
    private $requests;
    
    public function __construct(){
        $this->requests = curl_multi_init();
    }
    
    public function addRequest($request){
        curl_multi_add_handle($this->requests,$request);   
    }
    
    public function run(){
        $running_handles = null;

        do {
            $status_cme = curl_multi_exec($this->requests, $running_handles);
        } while ($status_cme == CURLM_CALL_MULTI_PERFORM);

        while ($running_handles && $status_cme == CURLM_OK) {
            if (curl_multi_select($this->requests) != -1) {
                do {
                    $status_cme = curl_multi_exec($this->requests, $running_handles);
                } while ($status == CURLM_CALL_MULTI_PERFORM);
            }
        }

        $res = array();
        foreach($this->requests as $k){        
            $error = curl_error($k);
            if(!empty($error)){
                $result ='';
                array_push($res,$result);
            }
            else{
                $content  = curl_multi_getcontent( $k );
                $result = curl_getinfo($k);
                $header_size = curl_getinfo($k, CURLINFO_HEADER_SIZE);
                $result['headers'] = substr($content, 0, $header_size);
                $result['content'] = substr($content, $header_size);
                $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
                array_push($res,$result);
            }

            // close current handler
            curl_multi_remove_handle($this->requests, $k );
        }
        curl_multi_close($mh); 
        return $res;
    }    
}
?>