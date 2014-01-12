<?php 
/**
* 
*/
class Attachment extends Object implements JsonSerializable
{
    /**
     * db id of the attachment 
     *
     * type: string
     */
    private $id = null;
    
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
     
     * The id of the exercise this attachment belongs to.
     *
     * type: string
     */
    private $exerciseId = null;
    
    /**
     * the $exerciseId getter
     *
     * @return the value of $exerciseId
     */ 
    public function getExerciseId(){
        return $this->exerciseId;
    }
    
    /**
     * the $exerciseId setter
     *
     * @param string $value the new value for $exerciseId
     */ 
    public function setExerciseId($value){
        $this->exerciseId = $value;
    }
    
    /**
     * The file of the attachment.
     *
     * type: File
     */
    private $file = null;
    
    /**
     * the $file getter
     *
     * @return the value of $file
     */ 
    public function getFile(){
        return $this->file;
    }
    
    /**
     * the $file setter
     *
     * @param string $value the new value for $file
     */ 
    public function setFile($value){
        $this->file = $value;
    }    

    
    /**
     * (description)
     */
    public static function getDbConvert()
    {
        return array(
           'A_id' => 'id',
           'E_id' => 'exerciseId',
           'F_file' => 'file'
        );
    }
    
    /**
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->id != null) $this->addInsertData($values, 'A_id', DBJson::mysql_real_escape_string($this->id));
        if ($this->exerciseId != null) $this->addInsertData($values, 'E_id', DBJson::mysql_real_escape_string($this->exerciseId));
        if ($this->file != null) $this->addInsertData($values, 'F_id', DBJson::mysql_real_escape_string($this->file->getFileId()));
        
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
        return 'A_id';
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
                if ($key == 'file'){
                    $this->{$key} = File::decodeFile($value,false);
                } else                
                    $this->{$key} = $value;
            }
        }
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     */
    public static function encodeAttachment($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
     */
    public static function decodeAttachment($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Attachment($value));
            }
            return $result;   
        } else
            return new Attachment($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'exerciseId' => $this->exerciseId,
            'file' => $this->file
        );
    }
}
?>