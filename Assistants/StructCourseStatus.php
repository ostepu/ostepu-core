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
    private $_course;
    
    /**
     * (description)
     */
    public function getCourse()
    {
        return $this->_course;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setCourse($value)
    {
        $this->_course = $value;
    }

    
    
    
    /**
     * a string that defines which status the user has in that course.
     *
     * type: string
     */
    private $_status;
    
    /**
     * (description)
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * (description)
     *
     * @param $param (description)
     */
    public function setStatus($value)
    {
        $this->_status = $value;
    }

    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'C_course' => '_course',
           'C_name' => '_status',
        );
    }
    
    /**
     * (description)
     */
    // TODO: hier fehlt noch der primary key/keys
    public static function getDbPrimaryKey()
    {
        return 'C_id';
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
    public static function encodeCourseStatus($_data)
    {
        return json_encode($_data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeCourseStatus($_data, $decode=true)
    {
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new CourseStatus($value));
            }
            return $result;   
        } else
            return new CourseStatus($_data);
    }
    
    /**
     * (description)
     */
    public function jsonSerialize()
    {
        return array(
            '_course' => $this->_course,
            '_status' => $this->_status
        );
    }
}
?>