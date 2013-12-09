<?php
/**
* 
*/
class Backup extends Object implements JsonSerializable
{   
    /**
     * a unique identifier for a backup
     *
     * type: string
     */
    private $_id;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }

    /**
     * the date on which the backup was created
     * 
     * type: date
     */
    private $_date;
    public function getDate(){
        return $this->_date;
    }
    public function setDate($_value){
        $this->_date = $_value;
    }

    /**
     * a file where the backup is stored
     *
     * type: File
     */
    private $_file;
    public function getFile(){
        return $this->_file;
    }
    public function setFile($_value){
        $this->_file = $_value;
    }
    
    
    public static function getDBConvert(){
        return array(
           'B_id' => '_id',
           'B_date' => '_date',
           'F_id_file' => '_file',
        );
    }
    public static function getDBPrimaryKey(){
        return 'B_id';
    }
   
   
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
             if (isset($_key)){
                $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeBackup($_data){
        return json_encode($_data);
    }
    
    public static function decodeBackup($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new Backup($_value));
            }
            return $result;   
        }
        else
            return new Backup($_data);
    }

    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_date' => $this->_date,
            '_file' => $this->_file
        );
    }
    
}
?>