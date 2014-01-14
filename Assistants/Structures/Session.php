<?php 
/**
* A pair of a course and a status for some user.
* The status reflects the rights the particular user has in that
* course
*/
class Session extends Object implements JsonSerializable
{
    /**
     * description)
     *
     * type: string
     */
    private $user;
    
    /**
     * (description)
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setUser($value)
    {
        $this->user = $value;
    }

    
    
    
    /**
     * a string that defines which status the user has in that course.
     *
     * type: string
     */
    private $status;
    
    /**
     * (description)
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setStatus($value)
    {
        $this->status = $value;
    }
   
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'U_id' => 'user',
           'SE_id' => 'session'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->user != null) $this->addInsertData($values, 'U_id', DBJson::mysql_real_escape_string($this->user));
        if ($this->session != null) $this->addInsertData($values, 'SE_id', DBJson::mysql_real_escape_string($this->session));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    }
    
    /**
     * (description)
     */
    // TODO: hier fehlt noch der primary key/keys
    public static function getDbPrimaryKey()
    {
        return 'SE_id';
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
    public static function encodeCourseStatus($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeCourseStatus($data, $decode=true)
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
     * (description)
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