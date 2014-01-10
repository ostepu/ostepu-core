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
    
    
    
    
    /**
     * (description)
     */  
    public static function getDBConvert(){
        return array(
           'M_id' => 'id',
           'U_id_tutor' => 'tutorId',
           'M_file' => 'file',
           'S_id' => 'submissionId',
           'M_tutorComment' => 'tutorComment',
           'M_outstanding' => 'outstanding',
           'M_status' => 'status',
           'M_points' => 'points',
           'M_date' => 'date'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'M_id', mysql_real_escape_string($this->id));
        if ($this->tutorId != null) $this->addInsertData($values, 'U_id_tutor', mysql_real_escape_string($this->tutorId));
        if ($this->file != array()) $this->addInsertData($values, 'F_id_file', mysql_real_escape_string($this->file->getFileId()));
        if ($this->submissionId != null) $this->addInsertData($values, 'S_id', mysql_real_escape_string($this->submissionId));
        if ($this->tutorComment != null) $this->addInsertData($values, 'M_tutorComment', mysql_real_escape_string($this->tutorComment));
        if ($this->outstanding != null) $this->addInsertData($values, 'M_outstanding', mysql_real_escape_string($this->outstanding));
        if ($this->status != null) $this->addInsertData($values, 'M_status', mysql_real_escape_string($this->status));
        if ($this->points != null) $this->addInsertData($values, 'M_points', mysql_real_escape_string($this->points));
        if ($this->date != null) $this->addInsertData($values, 'M_date', mysql_real_escape_string($this->date));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    } 
    
    /**
     * (description)
     */
    public static function getDBPrimaryKey(){
        return 'M_id';
    }
    
    /**
     * (description)
     */
    public static function getStatusDefinition(){
        return array(
            '0' => '???', // vorläufig
            '1' => '???', // endgültig
        );
    }
    
    /**
     * (description)
     */
    public function __construct($data=array()) {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if ($key == 'file'){
                    $this->{$key} = File::decodeFile($value, false);
                }
                else
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     */
    public static function encodeMarking($data){
        return json_encode($data);
    }
    
    /**
     * (description)
     */
    public static function decodeMarking($data){
        $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Marking($value));
            }
            return $result;   
        }
        else
            return new Marking($data);
    }
    
    /**
     * (description)
     */
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