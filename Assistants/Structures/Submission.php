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
           'F_id_file' => 'file',
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
    public static function getDbPrimaryKey()
    {
        return 'S_id';
    }
    
    public function jsonSerialize() {
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