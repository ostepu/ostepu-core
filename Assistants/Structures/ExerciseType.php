<?php 
/**
 * 
 */
class ExerciseType extends Object implements JsonSerializable
{
    /**
     * (description)
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
    
    
    
    
    private $name = null;
    
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
     * (description)
     * @param $param (description)
     */
    public function __construct($data=array()) 
    {
        foreach ($data AS $key => $value) {
            if (isset($key)){
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
           'ET_id' => 'id',
           'ET_name' => 'name'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'ET_id', mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'ET_name', mysql_real_escape_string($this->name));
        
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
        return 'ET_id';
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeExerciseType($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeExerciseType($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new ExerciseType($value));
            }
            return $result;   
        } else
            return new ExerciseType($data);
    }

    /**
     * (description)
     */
    public function jsonSerialize() 
    {
        return array(
            'id' => $this->id,
            'name' => $this->name
        );
    }
}
?>