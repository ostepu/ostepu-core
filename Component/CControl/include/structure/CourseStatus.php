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
    public function getCourse(){
        return $this->_course;
    }
    public function setCourse($_value){
        $this->_course = $_value;
    }

    /**
     * a string that defines which status the user has in that course.
     *
     * type: string
     */
    private $_status;
    public function getStatus(){
        return $this->_status;
    }
    public function setStatus($_value){
        $this->_status = $_value;
    }
    
    
    public static function getDBConvert(){
        return array(
           'C_course' => '_course',
           'C_name' => '_status',
        );
    }
    
    // TODO: hier fehlt noch der primary key/keys
    public static function getDBPrimaryKey(){
        return 'C_id';
    }
   
   
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
            if (isset($_key)){
                $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeCourseStatus($_data){
        return json_encode($_data);
    }
    
    public static function decodeCourseStatus($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new CourseStatus($_value));
            }
            return $result;   
        }
        else
            return new CourseStatus($_data);
    }
    
    public function jsonSerialize() {
        return array(
            '_course' => $this->_course,
            '_status' => $this->_status
        );
    }
}
?>