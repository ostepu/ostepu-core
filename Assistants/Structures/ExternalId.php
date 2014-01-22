<?php 
/**
 * @file ExternalId.php contains the ExternalId class
 */
 
/**
 * the external id structure
 *
 * @author Till Uhlig
 */
class ExternalId extends Object implements JsonSerializable
{
    /**
     * the db id of the external id
     *
     * type: string
     */
    private $id = null;
    
    /**
     * the $id getter
     *
     * @return the value of $id
     */ 
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */ 
    public function setId($value)
    {
        $this->id = $value;
    }
    
      
    /**
     * the corresponding course
     *
     * type: Course
     */
    private $course = null;
    
    /**
     * the $course getter
     *
     * @return the value of $course
     */ 
    public function getCourse()
    {
        return $this->course;
    }
    
    /**
     * the $course setter
     *
     * @param Course $value the new value for $course
     */ 
    public function setCourse($value)
    {
        $this->course = $value;
    }
    
    
    public function createExternalId($externalId,$courseId)
    {
        return new ExternalId(array('id' => $externalId,
        'course' => array('id' => $courseId)));
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
                if ($key == 'course'){
                    $this->{$key} = new Course($value,false);
                }
                else
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert()
    {
        return array(
           'EX_id' => 'id',
           'EX_course' => 'course'
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
        
        if ($this->id != null) $this->addInsertData($values, 'EX_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->course != null) $this->addInsertData($values, 'C_id', DBJson::mysql_real_escape_string($this->course->getId()));
        
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
        return 'EX_id';
    }
    
    /**
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeExternalId($data)
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
    public static function decodeExternalId($data, $decode=true)
    {
        if ($decode && $data==null) 
            $data = "{}";
    
        if ($decode)
            $data = json_decode($data);
            
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new ExternalId($value));
            }
            return $result;   
        } else
            return new ExternalId($data);
    }

    /**
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize()  
    {
        $list = array();
        if ($this->id!==null) $list['id'] = $this->id;
        if ($this->course!==null) $list['course'] = $this->course;
        return $list;
    }
}
?>