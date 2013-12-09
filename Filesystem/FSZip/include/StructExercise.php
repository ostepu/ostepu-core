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
    private $_id;
    public function getId(){
        return $this->_id;
    }
    public function setId($_value){
        $this->_id = $_value;
    }

    /**
     * The id of the course this exercise belongs to.
     *
     * type: string
     */
    private $_courseId;
    public function getCourseId(){
        return $this->_courseId;
    }
    public function setCourseId($_value){
        $this->_courseId = $_value;
    }

    /**
     * The id of the sheet this exercise is on.
     *
     * type: string
     */
    private $_sheetId;
    public function getSheetId(){
        return $this->_sheetId;
    }
    public function setSheetId($_value){
        $this->_sheetId = $_value;
    }

    /**
     * The maximum amount of points a student can reach in this exercise.
     *
     * type: decimal
     */
    private $_maxPoints;
    public function getMaxPoints(){
        return $this->_maxPoints;
    }
    public function setMaxPoints($_value){
        $this->_maxPoints = $_value;
    }

    /**
     * The type of points this exercise yields.
     *
     * type: string
     */
    private $_type;
    public function getType(){
        return $this->_type;
    }
    public function setType($_value){
        $this->_type = $_value;
    }

    /**
     * the submissions (?) for this exercise
     *
     * type: Submission[]
     */
    private $_submissions;
    public function getSubmissions(){
        return $_submissions;
    }
    public function setSubmissions($_value){
        $_submissions = $_value;
    }
    
    /**
     * a set of attachments that belong to this sheet
     *
     * type: File[]
     */
    private $_attachments = array();
    public function getAttachments(){
        return $this->_attachments;
    }
    public function setAttachments($_value){
        $this->_attachments = $_value;
    }
    
    
    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_courseId' => $this->_courseId,
            '_sheetId' => $this->_sheetId,
            '_maxPoints' => $this->_maxPoints,
            '_type' => $this->_type,
            '_submissions' => $_submissions,
            '_attachments' => $this->_attachments
        );
    }
}
?>