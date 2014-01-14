<?php
class Submission extends Object implements JsonSerializable
{
    /**
     * The identifier of this submission.
     */
    private $id;
    public function getId(){
        return $this->id;
    }
    public function setId($value){
        $this->id = $value;
    }

    /**
     * The id of the student that submitted his solution.
     *
     * type: string
     */
    private $studentId;
    public function getStudentId(){
        return $this->studentId;
    }
    public function setStudentId($value){
        $this->studentId = $value;
    }

    /**
     * a string that identifies the exercise this submission belongs to.
     *
     * type: string
     */
    private $exerciseId;
    public function getExerciseId(){
        return $this->exerciseId;
    }
    public function setExerciseId($value){
        $this->exerciseId = $value;
    }

    /**
     * A comment that a student made on his submission.
     *
     * type: string
     */
    private $comment;
    public function getComment(){
        return $this->comment;
    }
    public function setComment($value){
        $this->comment = $value;
    }
    
    /**
     * A students submission.
     *
     * type: File
     */
    private $file;
    public function getFile(){
        return $this->file;
    }
    public function setFile($value){
        $this->file = $value;
    }
    
    /**
     * If the submission has been accepted for marking.
     *
     * type: bool
     */
    private $accepted;
    public function getAccepted(){
        return $this->accepted;
    }
    public function setAccepted($value){
        $this->accepted = $value;
    }
    
    /**
     * If the submission has been selected as submission for the user's group
     *
     * type: bool
     */
    private $selectedForGroup;
    public function getSelectedForGroup(){
        return $this->selectedForGroup;
    }
    public function setSelectedForGroup($value){
        $this->selectedForGroup = $value;
    }
    
    /**
     * description
     *
     * type: date
     */
    private $date;
    public function getDate(){
        return $this->date;
    }
    public function setDate($value){
        $this->date = $value;
    }
    
    /**
     * (description)
     */  
    public static function getDbConvert()
    {
        return array(
           'S_id' => 'id',
           'U_id' => 'studentId',
           'S_file' => 'file',
           'E_id' => 'exerciseId',
           'S_comment' => 'comment',
           'S_accepted' => 'accepted',
           'S_date' => 'date',
           'S_selected' => 'selectedForGroup'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'S_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->studentId != null) $this->addInsertData($values, 'U_id', DBJson::mysql_real_escape_string($this->studentId));
        if ($this->file != array()) $this->addInsertData($values, 'F_id_file', DBJson::mysql_real_escape_string($this->file->getFileId()));
        if ($this->exerciseId != null) $this->addInsertData($values, 'E_id', DBJson::mysql_real_escape_string($this->exerciseId));
        if ($this->comment != null) $this->addInsertData($values, 'S_comment', DBJson::mysql_real_escape_string($this->comment));
        if ($this->accepted != null) $this->addInsertData($values, 'S_accepted', DBJson::mysql_real_escape_string($this->accepted));
        if ($this->date != null) $this->addInsertData($values, 'S_date', DBJson::mysql_real_escape_string($this->date));
        if ($this->selectedForGroup != null) $this->addInsertData($values, 'S_selected', DBJson::mysql_real_escape_string($this->selectedForGroup));
        
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
        return 'S_id';
    }
    
    /**
     * (description)
     */
    public function __construct($data=array()) {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if ($key == 'file'){
                    $this->{$key} = File::decodeFile($value, false);
                }
                else
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     */
    public static function encodeSubmission($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     */
    public static function decodeSubmission($data){
        $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Submission($value));
            }
            return $result;   
        }
        else
            return new Submission($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'studentId' => $this->studentId,
            'exerciseId' => $this->exerciseId,
            'comment' => $this->comment,
            'file' => $this->file,
            'accepted' => $this->accepted,
            'selectedForGroup' => $this->selectedForGroup,
            'date' => $this->date
        );
    }
}
?>