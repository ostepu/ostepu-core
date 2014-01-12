<?php 
/**
 * 
 */
class ExternalId extends Object implements JsonSerializable
{
    /**
     * (description)
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
     * (description)
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
     * @param string $value the new value for $course
     */ 
    public function setCourse($value)
    {
        $this->course = $value;
    }
    
    
    /**
     * (description)
     * @param $param (description)
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
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'EX_id' => 'id',
           'EX_course' => 'course'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'EX_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->course != null) $this->addInsertData($values, 'EX_course', DBJson::mysql_real_escape_string($this->course->getId()));
        
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
        return 'EX_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeExternalId($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeExternalId($data, $decode=true)
    {
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
     */
    public function jsonSerialize()  
    {
        return array(
            'id' => $this->id,
            'course' => $this->course
        );
    }
}
?>