<?php
class Marking extends Object implements JsonSerializable
{
    /**
     * The identifier of this marking.
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
     * The identifier of the submission this marking belongs to.
     *
     * type: string
     */
    private $submissionId;
    public function getSubmissionId(){
        return $submissionId;
    }
    public function setSubmissionId($value){
        $submissionId = $value;
    }
    
    /**
     * The id of the tutor that corrected the submission.
     * 
     * type: string
     */
    private $tutorId;
    public function getTutorId(){
        return $this->tutorId;
    }
    public function setTutorId($value){
        $this->tutorId = $value;
    }
    
    /**
     * a comment a tutor has made concerning a students submission.
     *
     * type: string
     */
    private $tutorComment;
    public function getTutorComment(){
        return $this->tutorComment;
    }
    public function setTutorComment($value){
        $this->tutorComment = $value;
    }
    
    /**
     * The file that contains the marked submission for the user.
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
     * The amount of points a student has reached with his submission.
     *
     * type: decimal
     */
    private $points;
    public function getPoints(){
        return $this->points;
    }
    public function setPoints($value){
        $this->points = $value;
    }

    /**
     * if the submission stands out from the other submissions.
     *
     * type: bool
     */
    private $outstanding;
    public function getOutstanding(){
        return $this->outstanding;
    }
    public function setOutstanding($value){
        $this->outstanding = $value;
    }
    
    /**
     * status
     *
     * type: string
     */
    private $status;
    public function getStatus(){
        return $this->status;
    }
    public function setStatus($value){
        $this->status = $value;
    }
    
    /**
     * 
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
    
    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'submissionId' => $submissionId,
            'tutorId' => $this->tutorId,
            'tutorComment' => $this->tutorComment,
            'file' => $this->file,
            'points' => $this->points,
            'outstanding' => $this->outstanding,
            'status' => $this->status,
            'date' => $this->date
        );
    }
}
?>