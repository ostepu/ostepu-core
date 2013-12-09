<?php
/**
* 
*/
class Course extends Object implements JsonSerializable
{
    /**
     * a string that identifies the course
     *
     * type: string
     */
    private $_id;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }

    /**
     * the name of the course
     *
     * type: string
     */
    private $_name;
    public function getName(){
        return $this->_name;
    }
    public function setName($_value){
        $this->_name = $_value;
    }

    /**
     * the semester in which the course is offered
     *
     * type: string
     */
    private $_semester;
    public function getSemester(){
        return $this->_semester;
    }
    public function setSemester($_value){
        $this->_semester = $_value;
    }

    /**
     * a set of ids of exercise sheets that belong to this course
     *
     * type: string[]
     */
    private $_exerciseSheets = array();
    public function getExerciseSheets(){
        return $this->_exerciseSheets;
    }
    public function setExerciseSheets($_value){
        $this->_exerciseSheets = $_value;
    }

    /**
     * the default size of groups in the course
     *
     * type: int
     */
    private $_defaultGroupSize;
    public function getDefaultGroupSize(){
        return $this->_defaultGroupSize;
    }
    public function setDefaultGroupSize($_value){
        $this->_defaultGroupSize = $_value;
    }
    
    
    public static function getDBConvert(){
        return array(
           'C_id' => '_id',
           'C_name' => '_name',
           'C_semester' => '_semester',
           'C_defaultGroupSize' => '_defaultGroupSize'
        );
    }
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
    
    public static function encodeCourse($_data){
        return json_encode($_data);
    }
    
    public static function decodeCourse($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new ExerciseSheets($_value));
            }
            return $result;   
        }
        else
            return new Link($_data);
    }

    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_name' => $this->_name,
            '_semester' => $this->_semester,
            '_exerciseSheets' => $this->_exerciseSheets,
            '_defaultGroupSize' => $this->_defaultGroupSize
        );
    }
}
?>