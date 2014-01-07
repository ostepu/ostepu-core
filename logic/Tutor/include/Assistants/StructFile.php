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
    
    /**
     * (description)
     */
    public function getFileId()
    {
        return $this->_fileId;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setFileId($value)
    {
        $this->_fileId = $value;
    }

    
    
    
    /**
     * The name that should be displayed for the file.
     *
     * type: string
     */
    private $_displayName=null;
    
    /**
     * (description)
     */
    public function getDisplayName()
    {
        return $this->_displayName;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setDisplayName($value)
    {
        $this->_displayName = $value;
    }

    
    
    
    /**
     * The URL of the file
     *
     * type: string
     */
    private $_address=null;
    
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
     * @param $param (description)
     */
    public function setAddress($value)
    {
        $this->_address = $value;
    }

    
    
    
    /**
     * When the file was created, this is necessary since the file might
     * be on another server as the server logic and/or interface.
     *
     * type: date/integer
     */
    private $_timeStamp=null;
    
    /**
     * (description)
     */
    public function getTimeStamp()
    {
        return $this->_timeStamp;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function seTimeStamp($value)
    {
        $this->_timeStamp = $value;
    }

    
    
    
    /**
     * the size of the file.
     *
     * type: decimal
     */
    private $_fileSize=null;
    
    /**
     * (description)
     */
    public function getFileSize()
    {
        return $this->_fileSize;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setFileSize($value)
    {
        $this->_fileSize = $value;
    }

    
    
    
    /**
     * hash of the file, ensures that the user has up-/downloaded the right
     * file.
     *
     * type: string/integer
     */
    private $_hash=null;
    
    /**
     * (description)
     */
    public function getHash()
    {
        return $this->_hash;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setHash($value)
    {
        $this->_hash = $value;
    }
    
    
    
    
     /**
     * content
     *
     * type: string
     */
    private $_body=null;
    
    /**
     * (description)
     */
    public function getBody()
    {
        return $this->_body;
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public function setBody($value)
    {
        $this->_body = $value;
    }
    
    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'F_id' => '_fileId',
           'F_displayName' => '_displayName',
           'F_address' => '_address',
           'F_timeStamp' => '_timeStamp',
           'F_fileSize' => '_fileSize',
           'F_hash' => '_hash',
           'F_body' => '_body'
        );
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
     * 
     * @param $param (description)
     */
    public static function encodeFile($_data)
    {
        return json_encode($_data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function decodeFile($_data)
    {
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new File($value));
            }
            return $result;   
        } else
            return new File($_data);
    }
    
    /**
     * (description)
     */   
    public function jsonSerialize()
    {
        return array(
            '_fileId' => $this->_fileId,
            '_displayName' => $this->_displayName,
            '_address' => $this->_address,
            '_timeStamp' => $this->_timeStamp,
            '_fileSize' => $this->_fileSize,
            '_hash' => $this->_hash,
            '_body' => $this->_body
        );
    }
}
?>