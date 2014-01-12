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
     * the $request getter
     *
     * @return the value of $request
     */ 
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * the $request setter
     *
     * @param string $value the new value for $idrequest
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
     * the $response getter
     *
     * @return the value of $response
     */ 
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * the $response setter
     *
     * @param string $value the new value for $response
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
     * the $errno getter
     *
     * @return the value of $errno
     */ 
    public function getErrno()
    {
        return $this->errno;
    }
    
    /**
     * the $errno setter
     *
     * @param string $value the new value for $errno
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
     * the $insertId getter
     *
     * @return the value of $insertId
     */ 
    public function getInsertId()
    {
        return $this->insertId;
    }
    
    /**
     * the $insertId setter
     *
     * @param string $value the new value for $insertId
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
     * the $affectedRows getter
     *
     * @return the value of $affectedRows
     */ 
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }
    
    /**
     * the $affectedRows setter
     *
     * @param string $value the new value for $affectedRows
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
     * the $numRows getter
     *
     * @return the value of $numRows
     */ 
    public function getNumRows()
    {
        return $this->numRows;
    }
    
    /**
     * the $numRows setter
     *
     * @param string $value the new value for $numRows
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
            
        $obj = new Query();
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
     * the json serialize function
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