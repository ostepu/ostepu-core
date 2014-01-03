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
     * (description)
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
     * (description)
     */
    public function getCourse()
    {
        return $this->course;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
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
        
        if ($this->id != null) $this->addInsertData($values, 'EX_id', $this->id );
        if ($this->course != null) $this->addInsertData($values, 'EX_course', $this->course->getId() );
        
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
     * (description)
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