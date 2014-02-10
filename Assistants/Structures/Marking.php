<?php
/**
 * @file Marking.php contains the Marking class
 */
 
/**
 * the marking structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class Marking extends Object implements JsonSerializable
{
    /**
     * @var string $id The identifier of this marking.
     *
     * type: string
     */
    private $id = null;
    
    /**
     * the $id getter
     *
     * @return the value of $id
     */ 
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * the $id setter
     *
     * @param string $value the new value for $id
     */ 
    public function setId($value)
    {
        $this->id = $value;
    }
    
    /**
     * @var Submission $submission The submission this marking belongs to.
     */
    private $submission = null;
    
    /**
     * the $submission getter
     *
     * @return the value of $submission
     */ 
    public function getSubmission()
    {
        return $submission;
    }
    
    /**
     * the $submission setter
     *
     * @param Submission $value the new value for $submission
     */ 
    public function setSubmission($value)
    {
        $submission = $value;
    }
    
    /**
     * @var string $tutorId The id of the tutor that corrected the submission.
     */
    private $tutorId = null;
    
    /**
     * the $tutorId getter
     *
     * @return the value of $tutorId
     */ 
    public function getTutorId()
    {
        return $this->tutorId;
    }
    
    /**
     * the $tutorId setter
     *
     * @param string $value the new value for $tutorId
     */ 
    public function setTutorId($value)
    {
        $this->tutorId = $value;
    }
    
    /**
     * @var string $tutorComment a comment a tutor has made concerning a students submission.
     */
    private $tutorComment = null;
    
    /**
     * the $tutorComment getter
     *
     * @return the value of $tutorComment
     */ 
    public function getTutorComment()
    {
        return $this->tutorComment;
    }
    
    /**
     * the $tutorComment setter
     *
     * @param string $value the new value for $tutorComment
     */ 
    public function setTutorComment($value)
    {
        $this->tutorComment = $value;
    }
    
    /**
     * @var file $file  The file that contains the marked submission for the user.
     */
    private $file = null;
    
    /**
     * the $file getter
     *
     * @return the value of $file
     */ 
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * the $file setter
     *
     * @param file $value the new value for $file
     */ 
    public function setFile($value)
    {
        $this->file = $value;
    }
    
    /**
     * @var int $points The amount of points a student has reached with his submission.
     *
     * type: int
     */
    private $points = null;
    
    /**
     * the $points getter
     *
     * @return the value of $points
     */ 
    public function getPoints()
    {
        return $this->points;
    }
    
    /**
     * the $points setter
     *
     * @param int $value the new value for $points
     */ 
    public function setPoints($value)
    {
        $this->points = $value;
    }

    /**
     * @var bool $outstanding if the submission stands out from the other submissions.
     */
    private $outstanding = null;
    
    /**
     * the $outstanding getter
     *
     * @return the value of $outstanding
     */ 
    public function getOutstanding()
    {
        return $this->outstanding;
    }
    
    /**
     * the $outstanding setter
     *
     * @param bool $value the new value for $outstanding
     */ 
    public function setOutstanding($value)
    {
        $this->outstanding = $value;
    }
    
    /**
     * @var string $prefix the marking status
     */
    private $status = null;
    
    /**
     * the $status getter
     *
     * @return the value of $status
     */ 
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * the $status setter
     *
     * @param string $value the new value for $status
     */ 
    public function setStatus($value)
    {
        $this->status = $value;
    }
    
    /**
     * @var date $date the date on which the marking was uploaded
     */
    private $date = null;
    
    /**
     * the $date getter
     *
     * @return the value of $date
     */ 
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * the $date setter
     *
     * @param date $value the new value for $date
     */ 
    public function setDate($value)
    {
        $this->date = $value;
    }
    
    
    /**
     * Creates an Marking object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $markingId The id of the marking.
     * @param string $tutorId The id of the tutor(User).
     * @param string $fileId The id of the file.
     * @param string $submissionId The id of the submission.
     * @param string $tutorComment The tutor comment.
     * @param string $outstanding The outstanding flag.
     * @param string $status The status flag.
     * @param string $points The points.
     * @param string $date The date.
     *
     * @return an marking object
     */
    public static function createMarking($markingId,$tutorId,$fileId,$submissionId,$tutorComment,
                                $outstanding,$status,$points,$date)
    {
        return new Marking(array('id' => $markingId,
        'tutorId' => $tutorId,
        'file' => new File(array('fileId' => $fileId)), 
        'submission' => new Submission(array('id' => $submissionId)), 
        'tutorComment' => $tutorComment, 
        'outstanding' => $outstanding, 
        'status' => $status, 
        'points' => $points, 
        'date' => $date));
    }
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert()
    {
        return array(
           'M_id' => 'id',
           'U_id_tutor' => 'tutorId',
           'M_file' => 'file',
           'M_submission' => 'submission',
           'M_tutorComment' => 'tutorComment',
           'M_outstanding' => 'outstanding',
           'M_status' => 'status',
           'M_points' => 'points',
           'M_date' => 'date'
        );
    }

    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData()
    {
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'M_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->tutorId != null) $this->addInsertData($values, 'U_id_tutor', DBJson::mysql_real_escape_string($this->tutorId));
        if ($this->file != null) $this->addInsertData($values, 'F_id_file', DBJson::mysql_real_escape_string($this->file->getFileId()));
        if ($this->submission != null) $this->addInsertData($values, 'S_id', DBJson::mysql_real_escape_string($this->submission->getId()));
        if ($this->tutorComment != null) $this->addInsertData($values, 'M_tutorComment', DBJson::mysql_real_escape_string($this->tutorComment));
        if ($this->outstanding != null) $this->addInsertData($values, 'M_outstanding', DBJson::mysql_real_escape_string($this->outstanding));
        if ($this->status != null) $this->addInsertData($values, 'M_status', DBJson::mysql_real_escape_string($this->status));
        if ($this->points != null) $this->addInsertData($values, 'M_points', DBJson::mysql_real_escape_string($this->points));
        if ($this->date != null) $this->addInsertData($values, 'M_date', DBJson::mysql_real_escape_string($this->date));
        
        if ($values != ""){
            $values=substr($values,1);
        }
        return $values;
    } 
    
    /**
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey()
    {
        return 'M_id';
    }
    
    /**
     * defines the marking status
     *
     * @return returns an mapping array
     */
    public static function getStatusDefinition()
    {
        return array(
            '0' => '???', // vorläufig
            '1' => '???', // endgültig
        );
    }
    
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array())
    {
        if ($data==null)
            $data = array();
        
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if ($key == 'file'){
                    $this->{$key} = File::decodeFile($value, false);
                }
                else
                if ($key == 'submission'){
                    $this->{$key} = Submission::decodeSubmission($value, false);
                }
                else
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * encodes an object to json
     * 
     * @param $data the object
     *
     * @return the json encoded object
     */
    public static function encodeMarking($data)
    {
        return json_encode($data);
    }
    
    /**
     * decodes $data to an object
     * 
     * @param string $data json encoded data (decode=true) 
     * or json decoded data (decode=false)
     * @param bool $decode specifies whether the data must be decoded
     *
     * @return the object
     */
    public static function decodeMarking($data, $decode=true)
    {
        if ($decode && $data==null) 
            $data = "{}";
    
        if ($decode)
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
     * the json serialize function
     */
    public function jsonSerialize()
    {
        $list = array();
        if ($this->id!==null) $list['id'] = $this->id;
        if ($this->submission!==null) $list['submission'] = $this->submission;
        if ($this->tutorId!==null) $list['tutorId'] = $this->tutorId;
        if ($this->tutorComment!==null) $list['tutorComment'] = $this->tutorComment;
        if ($this->file!==null) $list['file'] = $this->file;
        if ($this->points!==null) $list['points'] = $this->points;
        if ($this->outstanding!==null) $list['outstanding'] = $this->outstanding;
        if ($this->status!==null) $list['status'] = $this->status;
        if ($this->date!==null) $list['date'] = $this->date; 
        return $list;
    }
    
    public static function ExtractMarking($data, $singleResult = false)
    {
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert());
      
            // generates an assoc array of files by using a defined list of 
            // its attributes
            $files2 = DBJson::getObjectsByAttributes($data, 
                                            File::getDBPrimaryKey(), 
                                            File::getDBConvert(),
                                            '2');                             
                                            
            // generates an assoc array of a submission by using a defined 
            // list of its attributes
            $submissions = DBJson::getObjectsByAttributes($data,
                                    Submission::getDBPrimaryKey(), 
                                    Submission::getDBConvert(), 
                                    '2');
                                    
            // concatenates the submissions and the associated files
            $submissions = DBJson::concatObjectListsSingleResult($data, 
                            $submissions,
                            Submission::getDBPrimaryKey(),
                            Submission::getDBConvert()['S_file'] ,
                            $files2,
                            File::getDBPrimaryKey());  
 
            // sets the selectedForGroup attribute
            foreach ($submissions as &$submission){
                if (!isset($submission['selectedForGroup']) || $submission['selectedForGroup']==null){
                    if (!isset($submission['id']) || !isset($submission['selectedForGroup'])){
                        $submission['selectedForGroup'] = (string) 0;
                    } elseif ($submission['id'] == $submission['selectedForGroup']) {
                        $submission['selectedForGroup'] = (string) 1;
                    } else
                        $submission['selectedForGroup'] = (string) 0;
                }
                else
                    $submission['selectedForGroup'] = (string) 0;
            }
            
            // generates an assoc array of markings by using a defined list of 
            // its attributes
            $markings = DBJson::getObjectsByAttributes($data, 
                                    Marking::getDBPrimaryKey(), 
                                    Marking::getDBConvert());  

            // concatenates the markings and the associated files
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $markings,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_file'] ,
                            $files,
                            File::getDBPrimaryKey());
  
            // concatenates the markings and the associated submissions
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Marking::getDBPrimaryKey(),
                            Marking::getDBConvert()['M_submission'] ,
                            $submissions,
                            Submission::getDBPrimaryKey()); 
                   
            // to reindex
            $res = array_values($res);
            
            if ($singleResult==true){
                // only one object as result
                if (count($res)>0)
                    $res = $res[0];
            }
            
            return $res;
    }
}
?>