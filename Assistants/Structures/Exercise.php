<?php
/**
 * Contains all relevant Data for an exercise.
 */
class Exercise extends Object implements JsonSerializable
{
    // TODO
    // TODO wie werden in Exercise die Unteraufgaben eingebaut oder ist das unwichtig???
    // TODO

    /**
     * a string that identifies the exercise.
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
     * The id of the course this exercise belongs to.
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
     * The id of the sheet this exercise is on.
     *
     * type: string
     */
    private $sheetId;
    public function getSheetId(){
        return $this->sheetId;
    }
    public function setSheetId($value){
        $this->sheetId = $value;
    }

    /**
     * The maximum amount of points a student can reach in this exercise.
     *
     * type: decimal
     */
    private $maxPoints;
    public function getMaxPoints(){
        return $this->maxPoints;
    }
    public function setMaxPoints($value){
        $this->maxPoints = $value;
    }

    /**
     * The type of points this exercise yields.
     *
     * type: string
     */
    private $type;
    public function getType(){
        return $this->type;
    }
    public function setType($value){
        $this->type = $value;
    }

    /**
     * the submissions (?) for this exercise
     *
     * type: Submission[]
     */
    private $submissions;
    public function getSubmissions(){
        return $submissions;
    }
    public function setSubmissions($value){
        $submissions = $value;
    }
    
    /**
     * a set of attachments that belong to this sheet
     *
     * type: File[]
     */
    private $attachments = array();
    public function getAttachments(){
        return $this->attachments;
    }
    public function setAttachments($value){
        $this->attachments = $value;
    }
    
    
    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'sheetId' => $this->sheetId,
            'maxPoints' => $this->maxPoints,
            'type' => $this->type,
            'submissions' => $submissions,
            'attachments' => $this->attachments
        );
    }
}
?>