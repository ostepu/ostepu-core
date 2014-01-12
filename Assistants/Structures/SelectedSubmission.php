<?php
class SelectedSubmission extends Object implements JsonSerializable
{
    /**
     * The identifier of the group leader.
     *
     * type: string
     */
    private $leaderId;
    
    /**
     * the $leaderId getter
     *
     * @return the value of $leaderId
     */ 
    public function getLeaderId(){
        return $this->leaderId;
    }
    
    /**
     * the $leaderid setter
     *
     * @param string $value the new value for $leaderId
     */ 
    public function setLeaderId($value){
        $this->leaderId = $value;
    }

    /**
     * The id of the selected submission.
     *
     * type: string
     */
    private $submissionId;
    
    /**
     * the $submissionId getter
     *
     * @return the value of $submissionId
     */ 
    public function getSubmissionId(){
        return $this->submissionId;
    }
    
    /**
     * the $submissionId setter
     *
     * @param string $value the new value for $submissionId
     */ 
    public function setSubmissionId($value){
        $this->submissionId = $value;
    }

    /**
     * a string that identifies the exercise this submission belongs to.
     *
     * type: string
     */
    private $exerciseId;
    
    /**
     * the $exerciseId getter
     *
     * @return the value of $exerciseId
     */ 
    public function getExerciseId(){
        return $this->exerciseId;
    }
    
    /**
     * the $exerciseId setter
     *
     * @param string $value the new value for $exerciseId
     */ 
    public function setExerciseId($value){
        $this->exerciseId = $value;
    }

    
    
    
    /**
     * (description)
     */  
    public static function getDbConvert()
    {
        return array(
           'U_id_leader' => 'leaderId',
           'S_id_selected' => 'submissionId',
           'E_id' => 'exerciseId',
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->leaderId != null) $this->addInsertData($values, 'U_id_leader', DBJson::mysql_real_escape_string($this->leaderId));
        if ($this->submissionId != null) $this->addInsertData($values, 'S_id_selected', DBJson::mysql_real_escape_string($this->submissionId));
        if ($this->exerciseId != null) $this->addInsertData($values, 'E_id', DBJson::mysql_real_escape_string($this->exerciseId));
        
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
        return array('U_id_leader','S_id_selected');
    }
    
    /**
     * (description)
     */
    public function __construct($data=array()) {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     */
    public static function encodeSelectedSubmission($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     */
    public static function decodeSelectedSubmission($data){
        $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new SelectedSubmission($value));
            }
            return $result;   
        }
        else
            return new SelectedSubmission($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'leaderId' => $this->leaderId,
            'submissionId' => $this->submissionId,
            'exerciseId' => $this->exerciseId,
        );
    }
}
?>