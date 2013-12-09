<?php 
/**
 * 
 */
class Link extends Object implements JsonSerializable
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
    
    
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
            if (isset($_key)){
                $this->{$_key} = $_value;
            }
        }
    }
    
    
    public static function getDBConvert(){
        return array(
           'CL_id' => '_id',
           'CL_name' => '_name',
           'CL_address' => '_address',
        );
    }
    
    public static function getDBPrimaryKey(){
        return 'CL_id';
    }
    
    public static function encodeLink($_data){
        return json_encode($_data);
    }
    
    public static function decodeLink($_data, $decode=true){
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new Link($_value));
            }
            return $result;   
        }
        else
            return new Link($_data);
    }
    
    public function jsonSerialize() {
        return [
            '_id' => $this->_id,
            '_name' => $this->_name,
            '_address' => $this->_address
        ];
    }
}
?>