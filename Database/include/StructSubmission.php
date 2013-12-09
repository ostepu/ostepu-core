<?php
class Submission extends Object implements JsonSerializable
{
    /**
     * The identifier of this submission.
     */
    private $_id;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }

    /**
     * The id of the student that submitted his solution.
     *
     * type: string
     */
    private $_studentId;
    public function getStudentId(){
        return $this->_studentId;
    }
    public function setStudentId($_value){
        $this->_studentId = $_value;
    }

    /**
     * a string that identifies the exercise this submission belongs to.
     *
     * type: string
     */
    private $_exerciseId;
    public function getExerciseId(){
        return $this->_exerciseId;
    }
    public function setExerciseId($_value){
        $this->_exerciseId = $_value;
    }

    /**
     * A comment that a student made on his submission.
     *
     * type: string
     */
    private $_comment;
    public function getComment(){
        return $this->_comment;
    }
    public function setComment($_value){
        $this->_comment = $_value;
    }
    
    /**
     * A students submission.
     *
     * type: File
     */
    private $_file;
    public function getFile(){
        return $this->_file;
    }
    public function setFile($_value){
        $this->_file = $_value;
    }
    
    /**
     * If the submission has been accepted for marking.
     *
     * type: bool
     */
    private $_accepted;
    public function getAccepted(){
        return $this->_accepted;
    }
    public function setAccepted($_value){
        $this->_accepted = $_value;
    }
    
    /**
     * If the submission has been selected as submission for the user's group
     *
     * type: bool
     */
    private $_selectedForGroup;
    public function getSelectedForGroup(){
        return $this->_selectedForGroup;
    }
    public function setSelectedForGroup($_value){
        $this->_selectedForGroup = $_value;
    }
    
    /**
     * description
     *
     * type: date
     */
    private $_date;
    public function getDate(){
        return $this->_date;
    }
    public function setDate($_value){
        $this->_date = $_value;
    }
    
    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_studentId' => $this->_studentId,
            '_exerciseId' => $this->_exerciseId,
            '_comment' => $this->_comment,
            '_file' => $this->_file,
            '_accepted' => $this->_accepted,
            '_selectedForGroup' => $this->_selectedForGroup,
            '_date' => $this->_date
        );
    }
}
?>