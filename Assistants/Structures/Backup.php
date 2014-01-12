<?php
/**
* 
*/
class Backup extends Object implements JsonSerializable
{   
    /**
     * a unique identifier for a backup
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
     * the date on which the backup was created
     * 
     * type: date
     */
    private $date;
    
    /**
     * the $date getter
     *
     * @return the value of $date
     */ 
    public function getDate(){
        return $this->date;
    }
    
    /**
     * the $date setter
     *
     * @param string $value the new value for $date
     */ 
    public function setDate($value){
        $this->date = $value;
    }

    /**
     * a file where the backup is stored
     *
     * type: File
     */
    private $file;
    
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
    
    
    public static function getDBConvert(){
        return array(
           'B_id' => 'id',
           'B_date' => 'date',
           'F_id_file' => 'file',
        );
    }
    public static function getDBPrimaryKey(){
        return 'B_id';
    }
   
   
    public function __construct($data=array()) {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                $this->{$key} = $value;
            }
        }
    }
    
    public static function encodeBackup($data){
        return json_encode($data);
    }
    
    public static function decodeBackup($data){
        $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Backup($value));
            }
            return $result;   
        }
        else
            return new Backup($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'date' => $this->date,
            'file' => $this->file
        );
    }
    
}
?>