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
    
    /**
     * (description)
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setId($value)
    {
        $this->_id = $value;
    }

    
    
    
    /**
     * the name of the course
     *
     * type: string
     */
    private $_name;
    
    /**
     * (description)
     */
    public function getName()
    {
        return $this->_name;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setName($value)
    {
        $this->_name = $value;
    }

    
    
    
    /**
     * the semester in which the course is offered
     *
     * type: string
     */
    private $_semester;
    
    /**
     * (description)
     */
    public function getSemester()
    {
        return $this->_semester;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setSemester($value)
    {
        $this->_semester = $value;
    }

    
    
    
    /**
     * a set of ids of exercise sheets that belong to this course
     *
     * type: string[]
     */
    private $_exerciseSheets = array();
    
    /**
     * (description)
     */
    public function getExerciseSheets()
    {
        return $this->_exerciseSheets;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setExerciseSheets($value)
    {
        $this->_exerciseSheets = $value;
    }

    
    
    
    /**
     * the default size of groups in the course
     *
     * type: int
     */
    private $_defaultGroupSize;
    
    /**
     * (description)
     */
    public function getDefaultGroupSize()
    {
        return $this->_defaultGroupSize;
    }
    
    /**
     * (description)
     *
     * @param $_conf (description)
     */
    public function setDefaultGroupSize($value)
    {
        $this->_defaultGroupSize = $value;
    }
    
    
    
    
    /**
     * (description)
     */  
    public static function getDbConvert()
    {
        return array(
           'C_id' => '_id',
           'C_name' => '_name',
           'C_semester' => '_semester',
           'C_defaultGroupSize' => '_defaultGroupSize',
           'C_exerciseSheets' => '_exerciseSheets'
        );
    }
    
    /**
     * (description)
     */
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
                if (is_array($value)) {
                    $_sub = ExerciseSheet::decodeExerciseSheet($value);
                    $value = $_sub;
                }
                $this->{$_key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeCourse($_data)
    {
        return json_encode($_data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeCourse($_data, $decode=true)
    {
        if ($decode)
            $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $value) {
                array_push($result, new Course($value));
            }
            return $result;   
        } else
            return new Course($_data);
    }
    
    /**
     * (description)
     */
    public function jsonSerialize()
    {
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