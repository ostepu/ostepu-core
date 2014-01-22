<?php 
/**
* 
*/
class ApprovalCondition extends Object implements JsonSerializable
{
    /**
     * db id of the approval condition 
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
    public function setId($value){
        $this->id = $value;
    }
    
     /**
     
     * The id of the course this condition belongs to.
     *
     * type: string
     */
    private $courseId = null;
    
    /**
     * the $courseId getter
     *
     * @return the value of $courseId
     */ 
    public function getCourseId()
    {
        return $this->courseId;
    }
    
    /**
     * the $courseId setter
     *
     * @param string $value the new value for $courseId
     */ 
    public function setCourseId($value){
        $this->courseId = $value;
    }
    
    /**
     * The id of the exercise type this condition belongs to.
     *
     * type: string
     */
    private $exerciseTypeId = null;
    
    /**
     * the $exerciseTypeId getter
     *
     * @return the value of $exerciseTypeid
     */ 
    public function getExerciseTypeId()
    {
        return $this->exerciseTypeId;
    }
    
    /**
     * the $exerciseTypeid setter
     *
     * @param string $value the new value for $exerciseTypeId
     */ 
    public function setExerciseTypeId($value){
        $this->exerciseTypeId = $value;
    }    
    
    /**
     * (description)
     *
     * type: string
     */
    private $percentage = null;
    
    /**
     * the $percentage getter
     *
     * @return the value of $percentage
     */ 
    public function getPercentage()
    {
        return $this->percentage;
    }
    
    /**
     * the $percentage setter
     *
     * @param string $value the new value for $percentage
     */  
    public function setPercentage($value){
        $this->percentage = $value;
    }  

    
    public function createApprovalCondition($approvalConditionId,$courseId,$exerciseTypeId,$percentage)
    {
        return new ApprovalCondition(array('id' => $approvalConditionId,
        'courseId' => $courseId,
        'exerciseTypeId' => $exerciseTypeId, 
        'percentage' => $percentage));
    }
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'AC_id' => 'id',
           'C_id' => 'courseId',
           'ET_id' => 'exerciseTypeId',
           'AC_percentage' => 'percentage'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData()
    {
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'AC_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->courseId != null) $this->addInsertData($values, 'C_id', DBJson::mysql_real_escape_string($this->courseId));
        if ($this->exerciseTypeId != null) $this->addInsertData($values, 'ET_id', DBJson::mysql_real_escape_string($this->exerciseTypeId));
        if ($this->percentage != null) $this->addInsertData($values, 'AC_percentage', DBJson::mysql_real_escape_string($this->percentage));
        
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
        return 'AC_id';
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
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeApprovalCondition($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeApprovalCondition($data, $decode=true)
    {
        if ($decode && $data==null) 
            $data = "{}";
    
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new ApprovalCondition($value));
            }
            return $result;   
        } else
            return new ApprovalCondition($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        $list = array();
        if ($this->id!==null) $list['id'] = $this->id;
        if ($this->courseId!==null) $list['courseId'] = $this->courseId;
        if ($this->exerciseTypeId!==null) $list['exerciseTypeId'] = $this->exerciseTypeId;
        if ($this->percentage!==null) $list['percentage'] = $this->percentage;
        return $list; 
    }
}
?>