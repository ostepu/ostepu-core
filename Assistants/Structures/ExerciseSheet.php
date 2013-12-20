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
    
    
    
    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'ES_id' => 'id',
           'C_id' => 'courseId',
           'ES_startDate' => 'endDate',
           'ES_endDate' => 'startDate',
           'F_id_zip' => 'zipFile',
           'ES_groupSize' => 'groupSize',
           'F_id_sampleSolution' => 'sampleSolution',
           'F_id_file' => 'sheetFile',
           'ES_exercises' => 'exercises',
           'ES_name' => 'sheetName'
        );
    }
    
    /**
     * (description)
     */
    public static function getDbPrimaryKey()
    {
        return 'ES_id';
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
                if ($key == 'exercises') {
                    $this->{$key} = Exercise::decodeExercise($value, false);
                } elseif ($key == 'sheetFile' || $key == 'sampleSolution'){
                    $this->{$key} = File::decodeFile($value, false);
                } else{
                    $this->{$key} = $value;
                }
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeExerciseSheet($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeExerciseSheet($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new ExerciseSheet($value));
            }
            return $result;   
        } else
            return new ExerciseSheet($data);
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