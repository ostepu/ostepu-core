<?php 
/**
 * @file Session.php contains the Session class
 */
 
/**
 * the session structure
 *
 * @author Till Uhlig
 */
class Session extends Object implements JsonSerializable
{
    /**
     * @var string $user the db id of an user 
     */
    private $user;
    
    /**
     * the $user getter
     *
     * @return the value of $user
     */ 
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * the $user setter
     *
     * @param string $value the new value for $user
     */ 
    public function setUser($value)
    {
        $this->user = $value;
    }

    
    
    
    /**
     * @var string $status a string that defines which status the user has in that course.
     */
    private $status;
    
    /**
     * the $status getter
     *
     * @return the value of $status
     */ 
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * the $status setter
     *
     * @param string $value the new value for $status
     */ 
    public function setStatus($value)
    {
        $this->status = $value;
    }
   

    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert()
    {
        return array(
           'U_id' => 'user',
           'SE_sessionID' => 'session'
        );
    }
    
    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->user != null) $this->addInsertData($values, 'U_id', DBJson::mysql_real_escape_string($this->user));
        if ($this->session != null) $this->addInsertData($values, 'SE_sessionID', DBJson::mysql_real_escape_string($this->session));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    }
    
 
    /**
     * returns a sting/string[] of the database primary key/keys
     * @todo hier fehlt noch der primary key/keys
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'SE_id';
    }

    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array())
    {
        foreach ($data AS $key => $value) {
            if (isset($key)){
              /*  if ($key == "user"){
                    $this->{$key} = new User($value,false);
                }
                else*/
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
    public static function encodeSession($data)
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
    public static function decodeSession($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
            
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Session($value));
            }
            return $result;   
        } else
            return new Session($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize() 
    {
        return array(
            'user' => $this->user,
            'session' => $this->session
        );
    }
}
?>