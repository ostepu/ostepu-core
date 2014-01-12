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
     * (description)
     */
    public function getFileId()
    {
        return $this->fileId;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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
     * (description)
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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
     * (description)
     */
    public function getAddress()
    {
        return $this->address;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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
     * (description)
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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
     * (description)
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setFileSize($value)
    {
        $this->fileSize = $value;
    }

    
    
    
    /**
     * hash of the file, ensures that the user has up-/downloaded the right
     * file.
     *
     * type: string/integer
     */
    private $hash=null;
    
    /**
     * (description)
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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
     * (description)
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
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