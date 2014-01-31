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
    
    public function jsonSerialize() {
        return array(
            '_course' => $this->_course,
            '_status' => $this->_status
        );
    }
}
?>