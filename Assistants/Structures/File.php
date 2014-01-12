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
    private $fileId=null;
    
    /**
     * the $fileId getter
     *
     * @return the value of $fileId
     */ 
    public function getFileId()
    {
        return $this->fileId;
    }
    
    /**
     * the $fileId setter
     *
     * @param string $value the new value for $fileId
     */ 
    public function setFileId($value)
    {
        $this->fileId = $value;
    }

    
    
    
    /**
     * The name that should be displayed for the file.
     *
     * type: string
     */
    private $displayName=null;
    
    /**
     * the $displayName getter
     *
     * @return the value of $displayName
     */ 
    public function getDisplayName()
    {
        return $this->displayName;
    }
    
    /**
     * the $displayName setter
     *
     * @param string $value the new value for $displayName
     */ 
    public function setDisplayName($value)
    {
        $this->displayName = $value;
    }

    
    
    
    /**
     * The URL of the file
     *
     * type: string
     */
    private $address=null;
    
    /**
     * the $address getter
     *
     * @return the value of $address
     */ 
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * the $address setter
     *
     * @param string $value the new value for $address
     */ 
    public function setAddress($value)
    {
        $this->address = $value;
    }

    
    
    
    /**
     * When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
     *
     * type: date/integer
     */
    private $timeStamp=null;
    
    /**
     * the $timeStamp getter
     *
     * @return the value of $timeStamp
     */ 
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }
    
    /**
     * the $timeStamp setter
     *
     * @param string $value the new value for $timeStamp
     */ 
    public function seTimeStamp($value)
    {
        $this->timeStamp = $value;
    }

    
    
    
    /**
     * the size of the file.
     *
     * type: decimal
     */
    private $fileSize=null;
    
    /**
     * the $fileSize getter
     *
     * @return the value of $fileSize
     */ 
    public function getFileSize()
    {
        return $this->fileSize;
    }
    
    /**
     * the $fileSize setter
     *
     * @param string $value the new value for $fileSize
     */ 
    public function setFileSize($value)
    {
        $this->fileSize = $value;
    }

    
    
    
    /**
     * hash of the file, ensures that the user has up-/downloaded the right
     * file.
     *
     * type: string
     */
    private $hash=null;
    
    /**
     * the $hash getter
     *
     * @return the value of $hash
     */ 
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * the $hash setter
     *
     * @param string $value the new value for $hash
     */ 
    public function setHash($value)
    {
        $this->hash = $value;
    }
    
    
    
    
     /**
     * content
     *
     * type: string
     */
    private $body=null;
    
    /**
     * the $body getter
     *
     * @return the value of $body
     */ 
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * the $body setter
     *
     * @param string $value the new value for $body
     */ 
    public function setBody($value)
    {
        $this->body = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'F_id' => 'fileId',
           'F_displayName' => 'displayName',
           'F_address' => 'address',
           'F_timeStamp' => 'timeStamp',
           'F_fileSize' => 'fileSize',
           'F_hash' => 'hash',
           'F_body' => 'body'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->fileId != null) $this->addInsertData($values, 'F_id', DBJson::mysql_real_escape_string($this->fileId));
        if ($this->displayName != null) $this->addInsertData($values, 'F_displayName', DBJson::mysql_real_escape_string($this->displayName));
        if ($this->address != null) $this->addInsertData($values, 'F_address', DBJson::mysql_real_escape_string($this->address));
        if ($this->timeStamp != null) $this->addInsertData($values, 'F_timeStamp', DBJson::mysql_real_escape_string($this->timeStamp));
        if ($this->fileSize != null) $this->addInsertData($values, 'F_fileSize', DBJson::mysql_real_escape_string($this->fileSize));
        if ($this->hash != null) $this->addInsertData($values, 'F_hash', DBJson::mysql_real_escape_string($this->hash));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'F_id';
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
    public static function encodeFile($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function decodeFile($data, $decode=true)
    {   
        if ($decode == true)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new File($value));
            }
            return $result;   
        } else
            return new File($data);
    }
    
    /**
     * the json serialize function
     */  
    public function jsonSerialize()
    {
        return array(
            'fileId' => $this->fileId,
            'displayName' => $this->displayName,
            'address' => $this->address,
            'timeStamp' => $this->timeStamp,
            'fileSize' => $this->fileSize,
            'hash' => $this->hash,
            'body' => $this->body
        );
    }
}
?>