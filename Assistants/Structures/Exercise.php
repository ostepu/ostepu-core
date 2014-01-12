<?php
/**
 * Contains all relevant Data for an exercise.
 */
class Exercise extends Object implements JsonSerializable
{
    /**
     * a string that identifies the exercise.
     *
     * type: string
     */
    private $id;
    
    /**
     * the $id getter
     *
     * @return the value of $id
     */ 
    public function getId(){
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
     * The id of the course this exercise belongs to.
     *
     * type: string
     */
    private $courseId;
    
    /**
     * the $courseId getter
     *
     * @return the value of $courseId
     */ 
    public function getCourseId(){
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
     * The id of the sheet this exercise is on.
     *
     * type: string
     */
    private $sheetId;
    
    /**
     * the $sheetId getter
     *
     * @return the value of $sheetId
     */ 
    public function getSheetId(){
        return $this->sheetId;
    }
    
    /**
     * the $sheetId setter
     *
     * @param string $value the new value for $sheetId
     */
    public function setSheetId($value){
        $this->sheetId = $value;
    }

    /**
     * The maximum amount of points a student can reach in this exercise.
     *
     * type: decimal
     */
    private $maxPoints;
    
    /**
     * the $maxPoints getter
     *
     * @return the value of $maxPoints
     */ 
    public function getMaxPoints(){
        return $this->maxPoints;
    }
    
    /**
     * the $maxPoints setter
     *
     * @param string $value the new value for $maxPoints
     */
    public function setMaxPoints($value){
        $this->maxPoints = $value;
    }

    /**
     * The type of points this exercise yields.
     *
     * type: string
     */
    private $type;
    
    /**
     * the $type getter
     *
     * @return the value of $type
     */ 
    public function getType(){
        return $this->type;
    }
    
    /**
     * the $type setter
     *
     * @param string $value the new value for $type
     */
    public function setType($value){
        $this->type = $value;
    }
    
    /**
     * The type of points this exercise yields.
     *
     * type: int
     */
    private $link;
    
    /**
     * the $link getter
     *
     * @return the value of $link
     */ 
    public function getLink(){
        return $this->link;
    }
    
    /**
     * the $link setter
     *
     * @param string $value the new value for $link
     */
    public function setLink($value){
        $this->link = $value;
    }

    /**
     * the submissions (?) for this exercise
     *
     * type: Submission[]
     */
    private $submissions;
    
    /**
     * the $submissions getter
     *
     * @return the value of $submissions
     */ 
    public function getSubmissions(){
        return $submissions;
    }
    
    /**
     * the $submissions setter
     *
     * @param string $value the new value for $submissions
     */
    public function setSubmissions($value){
        $submissions = $value;
    }
    
    /**
     * a set of attachments that belong to this sheet
     *
     * type: File[]
     */
    private $attachments = array();
    
    /**
     * the $attachments getter
     *
     * @return the value of $attachments
     */ 
    public function getAttachments(){
        return $this->attachments;
    }
    
    /**
     * the $attachments setter
     *
     * @param string $value the new value for $attachments
     */
    public function setAttachments($value){
        $this->attachments = $value;
    }
    
    /**
     * (description)
     *
     * type: Bool
     */
    private $bonus = null;
    
    /**
     * the $bonus getter
     *
     * @return the value of $bonus
     */ 
    public function getBonus(){
        return $this->bonus;
    }
    
    /**
     * the $bonus setter
     *
     * @param string $value the new value for $bonus
     */
    public function setBonus($value){
        $this->bonus = $value;
    }
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'E_id' => 'id',
           'C_id' => 'courseId',
           'ES_id' => 'sheetId',
           'E_maxPoints' => 'maxPoints',
           'ET_id' => 'type',
           'E_id_link' => 'link',
           'E_submissions' => 'submissions',
           'E_bonus' => 'bonus',
           'E_attachments' => 'attachments'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'E_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->sheetId != null) $this->addInsertData($values, 'ES_id', DBJson::mysql_real_escape_string($this->sheetId));
        if ($this->maxPoints != null) $this->addInsertData($values, 'E_maxPoints', DBJson::mysql_real_escape_string($this->maxPoints));
        if ($this->type != null) $this->addInsertData($values, 'ET_id', DBJson::mysql_real_escape_string($this->type));
        if ($this->link != null) $this->addInsertData($values, 'E_id_link', DBJson::mysql_real_escape_string($this->link));
        if ($this->bonus != null) $this->addInsertData($values, 'E_bonus', DBJson::mysql_real_escape_string($this->bonus));
        
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
        return 'E_id';
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
                if ($key == 'submissions'){
                    $this->{$key} = Submission::decodeSubmission($value, false);
                }elseif ($key == 'attachments') {
                    $this->{$key} = File::decodeFile($value, false);
                } else
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeExercise($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeExercise($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Exercise($value));
            }
            return $result;   
        } else
            return new Exercise($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'sheetId' => $this->sheetId,
            'maxPoints' => $this->maxPoints,
            'type' => $this->type,
            'link' => $this->link,
            'submissions' => $this->submissions,
            'bonus' => $this->bonus,
            'attachments' => $this->attachments
        );
    }
}
?>