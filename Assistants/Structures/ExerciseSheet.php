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
    
    /**
     * the $id getter
     *
     * @return the value of $id
     */ 
    public function getId(){
        return $this->id;
    }
    
    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */ 
    public function setId($value){
        $this->id = $value;
    }
    
     /**
     * The id of the course this exercise belongs to.
     *
     * type: string
     */
    private $courseId;
    
    /**
     * the $courseId getter
     *
     * @return the value of $courseId
     */ 
    public function getCourseId(){
        return $this->courseId;
    }
    
    /**
     * the $courseId setter
     *
     * @param string $value the new value for $courseId
     */ 
    public function setCourseId($value){
        $this->courseId = $value;
    }

    /**
     * the date and time of the last submission
     *
     * type: date
     */
    private $endDate;
    
    /**
     * the $endDate getter
     *
     * @return the value of $endDate
     */ 
    public function getEndDate(){
        return $this->endDate;
    }
    
    /**
     * the $endDate setter
     *
     * @param string $value the new value for $endDate
     */ 
    public function setEndDate($value){
        $this->endDate = $value;
    }

    /**
     * the date and time the exercise sheet is shown to students
     *
     * type: date
     */
    private $startDate;
    
    /**
     * the $startDate getter
     *
     * @return the value of $startDate
     */ 
    public function getStartDate(){
        return $this->startDate;
    }
    
    /**
     * the $startDate setter
     *
     * @param string $value the new value for $startDate
     */ 
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
    
    /**
     * the $zipFile getter
     *
     * @return the value of $id
     */ 
    public function getZipFile(){
        return $this->zipFile;
    }
    
    /**
     * the $zipFile setter
     *
     * @param string $value the new value for $zipFile
     */ 
    public function setZipFile($value){
        $this->zipFile = $value;
    }
    
    /**
     * file that contains the sample solution
     *
     * type: File
     */
    private $sampleSolution;
    
    /**
     * the $sampleSolution getter
     *
     * @return the value of $sampleSolution
     */ 
    public function getSampleSolution(){
        return $this->sampleSolution;
    }
    
    /**
     * the $sampleSolution setter
     *
     * @param string $value the new value for $sampleSolution
     */ 
    public function setSampleSolution($value){
        $this->sampleSolution = $value;
    }

    /**
     * file that contains the exercise sheet
     *
     * type: File
     */
    private $sheetFile;
    
    /**
     * the $sheetFile getter
     *
     * @return the value of $sheetFile
     */ 
    public function getSheetFile(){
        return $this->sheetFile;
    }
    
    /**
     * the $sheetFile setter
     *
     * @param string $value the new value for $sheetFile
     */ 
    public function setSheetFile($value){
        $this->sheetFile = $value;
    }

    /**
     * a set of exercises that belong to this sheet
     *
     * type: Exercise[]
     */
    private $exercises = array();
    
    /**
     * the $exercises getter
     *
     * @return the value of $exercises
     */ 
    public function getExercises(){
        return $this->exercises;
    }
    
    /**
     * the $exercises setter
     *
     * @param string $value the new value for $exercises
     */ 
    public function setExercises($value){
        $this->exercises = $value;
    }

    /**
     * the maximum group size that is allowed for this exercise sheet
     *
     * type: integer
     */
    private $groupSize;
    
    /**
     * the $groupSize getter
     *
     * @return the value of $groupSize
     */ 
    public function getGroupSize(){
        return $this->groupSize;
    }
    
    /**
     * the $groupSize setter
     *
     * @param string $value the new value for $groupSize
     */ 
    public function setGroupSize($value){
        $this->groupSize = $value;
    }
    
    private $sheetName;
    
    /**
     * the $sheetName getter
     *
     * @return the value of $sheetName
     */ 
    public function getSheetName(){
        return $this->sheetName;
    }
    
    /**
     * the $sheetName setter
     *
     * @param string $value the new value for $sheetName
     */ 
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
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'ES_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->courseId != null) $this->addInsertData($values, 'C_id', DBJson::mysql_real_escape_string($this->courseId));
        if ($this->endDate != null) $this->addInsertData($values, 'ES_endDate', DBJson::mysql_real_escape_string($this->endDate));
        if ($this->startDate != null) $this->addInsertData($values, 'ES_startDate', DBJson::mysql_real_escape_string($this->startDate));
        if ($this->groupSize != null) $this->addInsertData($values, 'ES_groupSize', DBJson::mysql_real_escape_string($this->groupSize));
        if ($this->sheetName != null) $this->addInsertData($values, 'ES_name', DBJson::mysql_real_escape_string($this->sheetName));
        if ($this->sheetFile != null) $this->addInsertData($values, 'F_id_file', DBJson::mysql_real_escape_string($this->sheetFile->getFileId()));
        if ($this->sampleSolution != null) $this->addInsertData($values, 'F_id_sampleSolution', DBJson::mysql_real_escape_string($this->sampleSolution->getFileId()));
        
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
    
    /**
     * the json serialize function
     */
    public function jsonSerialize() 
    {
        return array(
            'id'  => $this->id,
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