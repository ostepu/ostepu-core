<?php 
/**
 * @file ExerciseType.php contains the ExerciseType class
 */
 
/**
 * the exercise type structure
 *
 * @author Till Uhlig
 */
class ExerciseType extends Object implements JsonSerializable
{
    /**
     * db id of the exercise type 
     *
     * type: string
     */
    private $id = null;
    
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
     * the exercise type name
     *
     * type: string
     */
    private $name = null;
    
    /**
     * the $name getter
     *
     * @return the value of $name
     */ 
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * the $name setter
     *
     * @param string $value the new value for $name
     */ 
    public function setName($value)
    {
        $this->name = $value;
    }
    
    
    public function createExerciseType($typeid,$name)
    {
        return new ExerciseType(array('id' => $typeid,
        'name' => $name));
    }
    
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
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
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert()
    {
        return array(
           'ET_id' => 'id',
           'ET_name' => 'name'
        );
    }
    
    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'ET_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->name != null) $this->addInsertData($values, 'ET_name', DBJson::mysql_real_escape_string($this->name));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    }
    
    /**
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'ET_id';
    }
    
    /**
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeExerciseType($data)
    {
        return json_encode($data);
    }
    
    /**
     * decodes $data to an object
     * 
     * @param string $data json encoded data (decode=true) 
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
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
     * the json serialize function
     *
     * @return an array to serialize the object
     */
    public function jsonSerialize()  
    {
        $list = array();
        if ($this->id!==null) $list['id'] = $this->id;
        if ($this->name!==null) $list['name'] = $this->name;
        return $list;
    }
}
?>