<?php
/**
 * @file Backup.php contains the Backup class
 */
 
/**
 * the backup structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class Backup extends Object implements JsonSerializable
{   
    /**
     * @var string $id a unique identifier for a backup
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
     * @var date $date the date on which the backup was created
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
     * @param date $value the new value for $date
     */ 
    public function setDate($value){
        $this->date = $value;
    }

    /**
     * @var file $file a file where the backup is stored
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
     * @param file $value the new value for $file
     */ 
    public function setFile($value){
        $this->file = $value;
    }
    
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert(){
        return array(
           'B_id' => 'id',
           'B_date' => 'date',
           'F_id_file' => 'file',
        );
    }
    
    /**
     * returns a sting/string[] of the database primary key/keys
     * 
     * @return the primary key/keys
     */
    public static function getDbPrimaryKey(){
        return 'B_id';
    }
   
   
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array()){
        foreach ($data AS $key => $value) {
             if (isset($key)){
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
    public static function encodeBackup($data){
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