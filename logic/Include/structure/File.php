<?php 
/**
* 
*/
class File extends Object implements JsonSerializable
{
    /**
     * An id that identifies the file.
     *
     * type: string
     */
    private $_fileId=null;
    public function getFileId(){
        return $this->_fileId;
    }
    public function setFileId($_value){
        $this->_fileId = $_value;
    }

    /**
     * The name that should be displayed for the file.
     *
     * type: string
     */
    private $_displayName=null;
    public function getDisplayName(){
        return $this->_displayName;
    }
    public function setDisplayName($_value){
        $this->_displayName = $_value;
    }

    /**
     * The URL of the file
     *
     * type: string
     */
    private $_address=null;
    public function getAddress(){
        return $this->_address;
    }
    public function setAddress($_value){
        $this->_address = $_value;
    }

    /**
     * When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
     *
     * type: date/integer
     */
    private $_timeStamp=null;
    public function getTimeStamp(){
        return $this->_timeStamp;
    }
    public function seTimeStamp($_value){
        $this->_timeStamp = $_value;
    }

    /**
     * the size of the file.
     *
     * type: decimal
     */
    private $_fileSize=null;
    public function getFileSize(){
        return $this->_fileSize;
    }
    public function setFileSize($_value){
        $this->_fileSize = $_value;
    }

    /**
     * hash of the file, ensures that the user has up-/downloaded the right
     * file.
     *
     * type: string/integer
     */
    private $_hash=null;
    public function getHash(){
        return $this->_hash;
    }
    public function setHash($_value){
        $this->_hash = $_value;
    }
    
     /**
     * content
     *
     * type: string
     */
    private $_body=null;
    public function getBody(){
        return $this->_body;
    }
    public function setBody($_value){
        $this->_body = $_value;
    }
        
   
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
            if (isset($_key)){
                $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeFile($_data){
        return json_encode($_data);
    }
    
    public static function decodeFile($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new File($_value));
            }
            return $result;   
        }
        else
            return new File($_data);
    }
        
    public function jsonSerialize() {
        return [
            '_fileId' => $this->_fileId,
            '_displayName' => $this->_displayName,
            '_address' => $this->_address,
            '_timeStamp' => $this->_timeStamp,
            '_fileSize' => $this->_fileSize,
            '_hash' => $this->_hash,
            '_body' => $this->_body
        ];
    }
}
?>