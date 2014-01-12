<?php 
/**
 * @file Group.php contains the Group class
 */
 
/**
 * the group structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class Group extends Object implements JsonSerializable
{
    /**
     * @var User[] $members all members of the group of the current users
     */
    private $members = array();
    
    /**
     * the $members getter
     *
     * @return the value of $members
     */
    public function getMembers(){
        return $this->members;
    }
    
    /**
     * the $members setter
     *
     * @param string $value the new value for $members
     */ 
    public function setMembers($value){
        $this->members = $value;
    }

    /**
     * @var User $leader the id of the user that is the leader of the group
     */
    private $leader = null;
    
    /**
     * the $leader getter
     *
     * @return the value of $leader
     */
    public function getLeader(){
        return $this->leader;
    }
    
    /**
     * the $leader setter
     *
     * @param string $value the new value for $leader
     */ 
    public function setLeader($value){
        $this->leader = $value;
    }

    /**
     * @var string $sheetId the id of the sheet for which this group exists
     */
    private $sheetId = null;
    
    /**
     * the $sheetId getter
     *
     * @return the value of $sheetId
     */
    public function getSheetId(){
        return $this->sheetId;
    }
    
    /**
     * the $sheetId setter
     *
     * @param string $value the new value for $sheetId
     */ 
    public function setSheetId($value){
        $this->sheetId = $value;
    }
    
    
    
     
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert(){
        return array(
           'U_member' => 'members',
           'U_leader' => 'leader',
           'ES_id' => 'sheetId',
        );
    }
    
    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->sheetId != null) $this->addInsertData($values, 'ES_id', DBJson::mysql_real_escape_string($this->sheetId));
        if ($this->members != array()) $this->addInsertData($values, 'U_id_leader', DBJson::mysql_real_escape_string($this->member[0]->getId()));
        if ($this->leader != null) $this->addInsertData($values, 'U_id_member', DBJson::mysql_real_escape_string($this->leader->getId()));
        
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
    public static function getDbPrimaryKey(){
        return array('U_id', 'ES_id');
    }
   
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array()){
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if ($key == 'leader' || $key == 'members'){
                    $this->{$key} = User::decodeUser($value, false);
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
    public static function encodeGroup($data){
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
    public static function decodeGroup($data){
        $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Group($value));
            }
            return $result;   
        }
        else
            return new Group($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'members' => $this->members,
            'leader' => $this->leader,
            'sheetId' => $this->sheetId
        );
    }
}
?>