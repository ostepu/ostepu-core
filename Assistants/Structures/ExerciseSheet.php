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
     * the date and time of the last submission
     *
     * type: date
     */
    private $endDate;
    public function getEndDate(){
        return $this->endDate;
    }
    public function setEndDate($value){
        $this->endDate = $value;
    }

    /**
     * the date and time the exercise sheet is shown to students
     *
     * type: date
     */
    private $startDate;
    public function getStartDate(){
        return $this->startDate;
    }
    public function setStartDate($value){
        $this->startDate = $value;
    }

    /**
     * a file that contains student submissions that were previosly
     * assinged to a tutor
     *
     * type: File
     */
    private $zipFile;
    public function getZipFile(){
        return $this->zipFile;
    }
    public function setZipFile($value){
        $this->zipFile = $value;
    }
    
    /**
     * file that contains the sample solution
     *
     * type: File
     */
    private $sampleSolution;
    public function getSampleSolution(){
        return $this->sampleSolution;
    }
    public function setSampleSolution($value){
        $this->sampleSolution = $value;
    }

    /**
     * file that contains the exercise sheet
     *
     * type: File
     */
    private $sheetFile;
    public function getSheetFile(){
        return $this->sheetFile;
    }
    public function setSheetFile($value){
        $this->sheetFile = $value;
    }

    /**
     * a set of exercises that belong to this sheet
     *
     * type: Exercise[]
     */
    private $exercises = array();
    public function getExercises(){
        return $this->exercises;
    }
    public function setExercises($value){
        $this->exercises = $value;
    }

    /**
     * the maximum group size that is allowed for this exercise sheet
     *
     * type: integer
     */
    private $groupSize;
    public function getGroupSize(){
        return $this->groupSize;
    }
    public function setGroupSize($value){
        $this->groupSize = $value;
    }
    
    private $sheetName;
    public function getSheetName(){
        return $this->sheetName;
    }
    public function setSheetName($value){
        $this->sheetName = $value;
    }

    public function jsonSerialize() {
        return array(
            'id' => $this->id,
            'courseId' => $this->courseId,
            'endDate' => $this->endDate,
            'startDate' => $this->startDate,
            'zipFile' => $this->zipFile,
            'sampleSolution' => $this->sampleSolution,
            'sheetFile' => $this->sheetFile,
            'exercises' => $this->exercises,
            'groupSize' => $this->groupSize,
            'sheetName' => $this->sheetName
        );
    }
}
?>