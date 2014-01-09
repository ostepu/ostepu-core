<?php 
/**
* 
*/
class ExerciseSheet extends Object implements JsonSerializable
{
    /**
     * a string that identifies the exercise sheet
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
     * the date and time of the last submission
     *
     * type: date
     */
    private $_endDate;
    public function getEndDate(){
        return $this->_endDate;
    }
    public function setEndDate($_value){
        $this->_endDate = $_value;
    }

    /**
     * the date and time the exercise sheet is shown to students
     *
     * type: date
     */
    private $_startDate;
    public function getStartDate(){
        return $this->_startDate;
    }
    public function setStartDate($_value){
        $this->_startDate = $_value;
    }

    /**
     * a file that contains student submissions that were previosly
     * assinged to a tutor
     *
     * type: File
     */
    private $_zipFile;
    public function getZipFile(){
        return $this->_zipFile;
    }
    public function setZipFile($_value){
        $this->_zipFile = $_value;
    }
    
    /**
     * file that contains the sample solution
     *
     * type: File
     */
    private $_sampleSolution;
    public function getSampleSolution(){
        return $this->_sampleSolution;
    }
    public function setSampleSolution($_value){
        $this->_sampleSolution = $_value;
    }

    /**
     * file that contains the exercise sheet
     *
     * type: File
     */
    private $_sheetFile;
    public function getSheetFile(){
        return $this->_sheetFile;
    }
    public function setSheetFile($_value){
        $this->_sheetFile = $_value;
    }

    /**
     * a set of exercises that belong to this sheet
     *
     * type: Exercise[]
     */
    private $_exercises = array();
    public function getExercises(){
        return $this->_exercises;
    }
    public function setExercises($_value){
        $this->_exercises = $_value;
    }

    /**
     * the maximum group size that is allowed for this exercise sheet
     *
     * type: integer
     */
    private $_groupSize;
    public function getGroupSize(){
        return $this->_groupSize;
    }
    public function setGroupSize($_value){
        $this->_groupSize = $_value;
    }

    public function jsonSerialize() {
        return array(
            '_id' => $this->_id,
            '_courseId' => $this->_courseId,
            '_endDate' => $this->_endDate,
            '_startDate' => $this->_startDate,
            '_zipFile' => $this->_zipFile,
            '_sampleSolution' => $this->_sampleSolution,
            '_sheetFile' => $this->_sheetFile,
            '_exercises' => $this->_exercises,
            '_groupSize' => $this->_groupSize
        );
    }
}
?>