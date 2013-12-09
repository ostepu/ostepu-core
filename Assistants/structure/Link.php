<?php 
/**
 * 
 */
class Link extends Object implements JsonSerializable
{
    /**
     * (description)
     */
    private $_id = null;
    
    /**
     * (description)
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setId($value)
    {
        $this->_id = $value;
    }
    
    
    
    
    private $_name = null;
    
    /**
     * (description)
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setName($value)
    {
        $this->_name = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $_address = null;
    
    /**
     * (description)
     */
    public function getAddress()
    {
        return $this->_address;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setAddress($value)
    {
        $this->_address = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $_relevanz = null;
    
    /**
     * (description)
     */
    public function getRelevanz()
    {
        return $this->$_relevanz;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setRelevanz($value)
    {
        $this->$_relevanz = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $_prefix = null;
    
    /**
     * (description)
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setPrefix($value)
    {
        $this->_prefix = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    private $_complete = null;
    
    /**
     * (description)
     */
    public function getcomplete()
    {
        return $this->$_complete;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setComplete($value)
    {
        $this->$_complete = $value;
    }    
    
    
    
    
    /**
     * (description)
     * @param $param (description)
     */
    public function __construct($_data=array()) 
    {
        foreach ($_data AS $_key => $value) {
            if (isset($_key)){
                $this->{$_key} = $value;
            }
        }
    }
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'CL_id' => '_id',
           'CL_name' => '_name',
           'CL_address' => '_address',
           'CL_prefix' => '_prefix',
           'CL_complete' => '_complete',
           'CL_relevanz' => '_relevanz',
        );
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'CL_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeLink($_data)
    {
        return json_encode($_data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeLink($_data, $decode=true)
    {
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new Link($value));
            }
            return $result;   
        } else
            return new Link($_data);
    }

    /**
     * (description)
     */
    public function jsonSerialize() 
    {
        return array(
            '_id' => $this->_id,
            '_name' => $this->_name,
            '_address' => $this->_address,
            '_prefix' => $this->_prefix,
            '_complete' => $this->_complete,
            '_relevanz' => $this->_relevanz
        );
    }
}
?>