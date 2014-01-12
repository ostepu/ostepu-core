<?php 
/**
* A pair of a course and a status for some user.
* The status reflects the rights the particular user has in that
* course
*/
class CourseStatus extends Object implements JsonSerializable
{
    /**
     * A course.
     *
     * type: Course
     */
    private $course;
    
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
     * a string that defines which status the user has in that course.
     *
     * type: string
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
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'CS_course' => 'course',
           'CS_status' => 'status'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->status != null) $this->addInsertData($values, 'CS_status', DBJson::mysql_real_escape_string($this->status));
        if ($this->course != null) $this->addInsertData($values, 'C_id', DBJson::mysql_real_escape_string($this->course->getId()));
        
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
        return array('C_id', 'U_id');
    }
    
    /**
     * (description)
     */
    public static function getStatusDefinition(){
        return array(
            '0' => 'student',
            '1' => 'tutor',
            '2' => 'lecturer',
            '3' => 'administrator',
            '4' => 'super-administrator'
        );
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
                if ($key == 'course'){
                    $this->{$key} = Course::decodeCourse($value, false);
                }
                else{
                    $this->{$key} = $value;
                }
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
                array_push($result, new CourseStatus($value));
            }
            return $result;   
        } else
            return new CourseStatus($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize() 
    {
        return array(
            'course' => $this->course,
            'status' => $this->status
        );
    }
}
?>