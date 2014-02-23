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
     * type: String[]
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
     * type: int
     */
    private $errno = null;
    
    /**
     * (description)
     */
    public function getErrno()
    {
        return $this->errno;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setErrno($value)
    {
        $this->errno = $value;
    }  
    
    /**
     * (description)
     *
     * type: int
     */
    private $insertId = null;
    
    /**
     * (description)
     */
    public function getInsertId()
    {
        return $this->insertId;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setInsertId($value)
    {
        $this->insertId = $value;
    } 
    
    /**
     * (description)
     *
     * type: int
     */
    private $affectedRows = null;
    
    /**
     * (description)
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setAffectedRows($value)
    {
        $this->affectedRows = $value;
    } 
    
    /**
     * (description)
     *
     * type: int
     */
    private $numRows = null;
    
    /**
     * (description)
     */
    public function getNumRows()
    {
        return $this->numRows;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setNumRows($value)
    {
        $this->numRows = $value;
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

        } else {
            $obj = new Query();
            if (isset($data['request']))
                $obj->setRequest(json_decode(json_encode($data['request'])));
            if (isset($data['response']))
                $obj->setResponse($data['response']);                
            if (isset($data['affectedRows']))
                $obj->setAffectedRows($data['affectedRows']); 
            if (isset($data['insertId']))
                $obj->setInsertId($data['insertId']); 
            if (isset($data['errno']))
                $obj->setErrno($data['errno']); 
            if (isset($data['numRows']))
                $obj->setNumRows($data['numRows']); 
        }
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
        if ($this->affectedRows!==null) $list['affectedRows'] = $this->affectedRows;
        if ($this->insertId!==null) $list['insertId'] = $this->insertId;
        if ($this->errno!==null) $list['errno'] = $this->errno;
        if ($this->numRows!==null) $list['numRows'] = $this->numRows;
        return $list;
    }
}
?>