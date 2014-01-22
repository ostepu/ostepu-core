<?php 
/**
 * @file File.php contains the File class
 */
 
/**
 * the file structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class File extends Object implements JsonSerializable
{
    /**
     * @var string $fileId An id that identifies the file.
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
     * @var string $displayName The name that should be displayed for the file.
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
     * @var string $address The URL of the file
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
     * @var date $timeStamp When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
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
     * @param date $value the new value for $timeStamp
     */ 
    public function seTimeStamp($value)
    {
        $this->timeStamp = $value;
    }

    
    
    
    /**
     * @var int $fileSize the size of the file.
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
     * @param int $value the new value for $fileSize
     */ 
    public function setFileSize($value)
    {
        $this->fileSize = $value;
    }

    
    
    
    /**
     * @var string $hash hash of the file, ensures that the user has up-/downloaded the right
     * file.
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
     * @var string $body content (base64 encoded)
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
    
    
    public function createFile($fileId,$displayName,$address,$timeStamp,$fileSize,$hash)
    {
        return new File(array('fileId' => $fileId,
        'displayName' => $displayName,
        'address' => $address, 
        'timeStamp' => $timeStamp,
        'fileSize' => $fileSize, 
        'hash' => $hash));
    }
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
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
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData()
    {
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
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'F_id';
    } 
           
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array())
    {
        if ($data==null)
            $data = array();
        
        foreach ($data AS $key => $value) {
            if (isset($key)){
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeFile($data)
    {
        return json_encode($data);
    }

    /**
     * decodes $data to an object
     * 
     * @param string $data json encoded data (decode=true) 
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeFile($data, $decode=true)
    {   
        if ($decode && $data==null) 
            $data = "{}";
            
        if ($decode)
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
        $list = array();
        if ($this->fileId!==null) $list['fileId'] = $this->fileId;
        if ($this->displayName!==null) $list['displayName'] = $this->displayName;
        if ($this->address!==null) $list['address'] = $this->address;
        if ($this->timeStamp!==null) $list['timeStamp'] = $this->timeStamp;
        if ($this->fileSize!==null) $list['fileSize'] = $this->fileSize;
        if ($this->hash!==null) $list['hash'] = $this->hash;
        if ($this->body!==null) $list['body'] = $this->body;
        return $list; 
    }
}
?>