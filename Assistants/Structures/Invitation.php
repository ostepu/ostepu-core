<?php
/**
 * @file Invitation.php contains the Invitation class
 */
 
/**
 * the invitation structure
 *
 * @author Till Uhlig, Florian Lücke
 */
class Invitation extends Object implements JsonSerializable
{
    /**
     * @var User $member the user that was invited
     */
    private $member;
    
    /**
     * the $member getter
     *
     * @return the value of $user
     */ 
    public function getMember(){
        return $this->member;
    }
    
    /**
     * the $member setter
     *
     * @param string $value the new value for $member
     */ 
    public function setMember($value){
        $this->member = $value;
    }

    /**
     * @var User $leader the user that created the group
     */
    private $leader;
    
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
     * @var string $sheet the exercise sheet id 
     */
    private $sheet;
    
    /**
     * the $sheet getter
     *
     * @return the value of $sheet
     */ 
    public function getSheet(){
        return $this->sheet;
    }
    
    /**
     * the $sheet setter
     *
     * @param string $value the new value for $sheet
     */ 
    public function setSheet($value){
        $this->sheet = $value;
    }
    
    
    
    
    
    /**
     * returns an mapping array to convert between database and structure
     *
     * @return the mapping array
     */
    public static function getDbConvert()
    {
        return array(
           'U_leader' => 'leader',
           'U_member' => 'member',
           'ES_id' => 'sheet'
        );
    }
    
    /**
     * converts an object to insert/update data
     *
     * @return a comma separated string e.g. "a=1,b=2"
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->sheet != null) $this->addInsertData($values, 'ES_id', DBJson::mysql_real_escape_string($this->sheet));
        if ($this->leader != null) $this->addInsertData($values, 'U_id_member', DBJson::mysql_real_escape_string($this->leader->getId()));
        if ($this->member != null) $this->addInsertData($values, 'U_id_leader', DBJson::mysql_real_escape_string($this->member->getId()));
        
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
        return array('U_id','ES_id','U_id2');
    }
    
    /**
     * the constructor
     * 
     * @param $data an assoc array with the object informations
     */
    public function __construct($data=array())
    {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if ($key == 'member' || $key == 'leader') {
                    $this->{$key} = User::decodeUser($value, false);
                } else{
                    $this->{$key} = $value;
                }
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
    public static function encodeInvitation($data)
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
    public static function decodeInvitation($data, $decode=true)
    {
        if ($decode)
            $data = json_decode($data);
        if (is_array($data)){
            $result = array();
            foreach ($data AS $key => $value) {
                array_push($result, new Invitation($value));
            }
            return $result;   
        } else
            return new Invitation($data);
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'user' => $this->user,
            'leader' => $this->leader,
            'sheet' => $this->sheet
        );
    }
}
?>