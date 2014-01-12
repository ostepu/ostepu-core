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
     * the name of the course
     *
     * type: string
     */
    private $name;
    
    /**
     * the $name setter
     *
     * @param string $value the new value for $name
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
     * the $semester getter
     *
     * @return the value of $semester
     */ 
    public function getSemester()
    {
        return $this->semester;
    }
    
    /**
     * the $semester setter
     *
     * @param string $value the new value for $semester
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
     * the $exerciseSheets getter
     *
     * @return the value of $exerciseSheets
     */ 
    public function getExerciseSheets()
    {
        return $this->exerciseSheets;
    }
    
    /**
     * the $exerciseSheets setter
     *
     * @param string $value the new value for $exerciseSheets
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
     * the $defaultGroupSize getter
     *
     * @return the value of $defaultGroupSize
     */ 
    public function getDefaultGroupSize()
    {
        return $this->defaultGroupSize;
    }
    
    /**
     * the $defaultGroupSize setter
     *
     * @param string $value the new value for $defaultGroupSize
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
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'C_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'C_name', DBJson::mysql_real_escape_string($this->name));
        if ($this->semester != null) $this->addInsertData($values, 'C_semester', DBJson::mysql_real_escape_string($this->semester));
        if ($this->defaultGroupSize != null) $this->addInsertData($values, 'C_defaultGroupSize', DBJson::mysql_real_escape_string($this->defaultGroupSize));
        
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
     * the json serialize function
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