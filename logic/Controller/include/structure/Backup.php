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

    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_date' => $this->_date,
            '_file' => $this->_file
        );
    }
    
}
?>