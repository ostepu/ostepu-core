<?php
class Marking extends Object implements JsonSerializable
{
    /**
     * THe identifier of this marking.
     *
     * type: string
     */
    private $_id;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }
    
    /**
     * The identifier of the submission this marking belongs to.
     *
     * type: string
     */
    private $_submissionId;
    public function getSubmissionId(){
        return $_submissionId;
    }
    public function setSubmissionId($_value){
        $_submissionId = $_value;
    }
    
    /**
     * The id of the tutor that corrected the submission.
     * 
     * type: string
     */
    private $_tutorId;
    public function getTutorId(){
        return $this->_tutorId;
    }
    public function setTutorId($_value){
        $this->_tutorId = $_value;
    }
    
    /**
     * a comment a tutor has made concerning a students submission.
     *
     * type: string
     */
    private $_tutorComment;
    public function getTutorComment(){
        return $this->_tutorComment;
    }
    public function setTutorComment($_value){
        $this->_tutorComment = $_value;
    }
    
    /**
     * The file that contains the marked submission for the user.
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
     * The amount of points a student has reached with his submission.
     *
     * type: decimal
     */
    private $_points;
    public function getPoints(){
        return $this->_points;
    }
    public function setPoints($_value){
        $this->_points = $_value;
    }

    /**
     * if the submission stands out from the other submissions.
     *
     * type: bool
     */
    private $_outstanding;
    public function getOutstanding(){
        return $this->_outstanding;
    }
    public function setOutstanding($_value){
        $this->_outstanding = $_value;
    }
    
    /**
     * status
     *
     * type: string
     */
    private $_status;
    public function getStatus(){
        return $this->_status;
    }
    public function setStatus($_value){
        $this->_status = $_value;
    }
    
    /**
     * 
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
            '_submissionId' => $_submissionId,
            '_tutorId' => $this->_tutorId,
            '_tutorComment' => $this->_tutorComment,
            '_file' => $this->_file,
            '_points' => $this->_points,
            '_outstanding' => $this->_outstanding,
            '_status' => $this->_status,
            '_date' => $this->_date
        );
    }
}
?>