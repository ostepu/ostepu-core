<?php 
/**
 * 
 */
class Query extends Object implements JsonSerializable
{

    /**
     * (description)
     *
     * type: String
     */
    private $request = null;
    
    /**
     * (description)
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setRequest($value)
    {
        $this->request = $value;
    }
    
    
    
    
        /**
     * (description)
     *
     * type: String[][]
     */
    private $response = array();
    
    /**
     * (description)
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setResponse($value)
    {
        $this->response = $value;
    }
    
    
    
    
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function __construct($data=array()) 
    {
        foreach ($data AS $key => $value) {
            if (isset($key)){
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeQuery($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeQuery($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data,true);
            
        $obj = null;
        if (is_array($data) && !isset($data['response']) && !isset($data['request'])){

        }
        else
        {
        $obj = new Query();
        if (isset($data['request']))
            $obj->setRequest(json_decode(json_encode($data['request'])));
        if (isset($data['response']))
            $obj->setResponse($data['response']);        
        }
    
       /* if ($decode)
            $data = json_decode($data);
            
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Query($value));
            }
            return $result;   
        } else
            return new Query($data);*/
            return $obj;
    }
    
    /**
     * (description)
     */
    public function jsonSerialize() 
    {       
         $list = array();
         if ($this->request!==null) $list['request'] = $this->request;
         if ($this->response!==array()) $list['response'] = $this->response;
       return $list;
    }
}
?>