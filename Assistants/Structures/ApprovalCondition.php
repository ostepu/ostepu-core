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
    private $id;
    public function getId(){
        return $this->id;
    }
    public function setId($value){
        $this->id = $value;
    }
    
     /**
     
     * The id of the course this condition belongs to.
     *
     * type: string
     */
    private $courseId;
    public function getCourseId(){
        return $this->courseId;
    }
    public function setCourseId($value){
        $this->courseId = $value;
    }
    
    /**
     * The id of the exercise type this condition belongs to.
     *
     * type: string
     */
    private $exerciseTypeId;
    public function getExerciseTypeId(){
        return $this->exerciseTypeId;
    }
    public function setExerciseTypeId($value){
        $this->exerciseTypeId = $value;
    }    
    
    /**
     * (description)
     *
     * type: string
     */
    private $percentage;
    public function getPercentage(){
        return $this->percentage;
    }
    public function setPercentage($value){
        $this->percentage = $value;
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
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'AC_id', $this->id );
        if ($this->courseId != null) $this->addInsertData($values, 'C_id', $this->courseId );
        if ($this->exerciseTypeId != null) $this->addInsertData($values, 'ET_id', $this->exerciseTypeId );
        if ($this->percentage != null) $this->addInsertData($values, 'AC_percentage', $this->percentage );
        
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
    
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'exerciseSheetId' => $this->exerciseSheetId,
            'percentage' => $this->percentage
        );
    }
}
?>