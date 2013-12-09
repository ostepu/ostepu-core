<?php
/**
 * 
 */
class Component extends Object implements JsonSerializable
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
    
    
    
    
    /**
     * (description)
     */
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
    private $_option = null;
    
    /**
     * (description)
     */
    public function getOption()
    {  
        return $this->_option;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setOption($value)
    {
        $this->_option = $value;
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
    private $_links = null;
    
    /**
     * (description)
     */
    public function getLinks()
    {
        return $this->_links;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setLinks($value)
    {
        $this->_links = $value;
    }


    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'CO_id' => '_id',
           'CO_name' => '_name',
           'CO_address' => '_address',
           'CO_option' => '_option',
           'CO_prefix' => '_prefix',
           'CO_links' => '_links'
        );
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'CO_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function __construct($_data=array()) 
    {
        foreach ($_data AS $_key => $value) {
            if (isset($_key)){
                if (is_array($value)) {
                    $_sub =  Link::decodeLink($value,false);
                    $value = $_sub;
                }
            $this->{$_key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeComponent($_data)
    {
        return json_encode($_data);
    }

    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeComponent($_data, $decode=true)
    {
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new Component($value));
            }
            return $result;   
        } else
            return new Component($_data);
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
            '_option' => $this->_option,
            '_prefix' => $this->_prefix,
            '_links' => $this->_links
        );
    }

}
?>