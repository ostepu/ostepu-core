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
    private $id;
    
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
     * the name of the course
     *
     * type: string
     */
    private $name;
    
    /**
     * (description)
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setName($value)
    {
        $this->name = $value;
    }

    
    
    
    /**
     * the semester in which the course is offered
     *
     * type: string
     */
    private $semester;
    
    /**
     * (description)
     */
    public function getSemester()
    {
        return $this->semester;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setSemester($value)
    {
        $this->semester = $value;
    }

    
    
    
    /**
     * a set of ids of exercise sheets that belong to this course
     *
     * type: string[]
     */
    private $exerciseSheets = array();
    
    /**
     * (description)
     */
    public function getExerciseSheets()
    {
        return $this->exerciseSheets;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setExerciseSheets($value)
    {
        $this->exerciseSheets = $value;
    }

    
    
    
    /**
     * the default size of groups in the course
     *
     * type: int
     */
    private $defaultGroupSize;
    
    /**
     * (description)
     */
    public function getDefaultGroupSize()
    {
        return $this->defaultGroupSize;
    }
    
    /**
     * (description)
     *
     * @param $conf (description)
     */
    public function setDefaultGroupSize($value)
    {
        $this->defaultGroupSize = $value;
    }
    
    
    /**
     * (description)
     */  
    public static function getDbConvert()
    {
        return array(
           'C_id' => 'id',
           'C_name' => 'name',
           'C_semester' => 'semester',
           'C_defaultGroupSize' => 'defaultGroupSize',
           'C_exerciseSheets' => 'exerciseSheets'
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
    public function __construct($data=array()) 
    {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if (is_array($value)) {
                    $sub = ExerciseSheet::decodeExerciseSheet($value, false);
                    $value = $sub;
                }
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeCourse($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeCourse($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Course($value));
            }
            return $result;   
        } else
            return new Course($data);
    }
    
    /**
     * (description)
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'semester' => $this->semester,
            'exerciseSheets' => $this->exerciseSheets,
            'defaultGroupSize' => $this->defaultGroupSize
        );
    }
}
?>