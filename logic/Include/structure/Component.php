<?php
/**
 * 
 */
class Component extends Object implements JsonSerializable
{
    private $_id = null;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }
    
    private $_name = null;
    public function getName(){
        return $this->_name;
    }
    public function setName($_value){
        $this->_name = $_value;
    }
    
    private $_address = null;
    public function getAddress(){
        return $this->_address;
    }
    public function setAddress($_value){
        $this->_address = $_value;
    }
    
    private $_option = null;
    public function getOption(){  
        return $this->_option;
    }
    public function setOption($_value){
        $this->_option = $_value;
    }
    
    private $_prefix = null;
    public function getPrefix(){
        return $this->_prefix;
    }
    public function setPrefix($_value){
        $this->_prefix = $_value;
    }
    
    private $_links = null;
    public function getLinks(){
        return $this->_links;
    }
    public function setLinks($_value){
        $this->_links = $_value;
    }

    
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
            if (isset($_key)){
                if (is_array($_value)) {
                    $_sub = new Link($_value);
                    $this->_value = $_sub;
                }
            $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeComponent($_data){
        return json_encode($_data);
    }
    
    public static function decodeComponent($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new Component($_value));
            }
            return $result;   
        }
        else
            return new Component($_data);
    }
    
    public function jsonSerialize() {
        return [
            '_id' => $this->_id,
            '_name' => $this->_name,
            '_address' => $this->_address,
            '_option' => $this->_option,
            '_prefix' => $this->_prefix,
            '_links' => $this->_links
        ];
    }

}
?>