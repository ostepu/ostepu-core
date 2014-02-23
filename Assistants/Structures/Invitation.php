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
    private $member = null;
    
    /**
     * the $member getter
     *
     * @return the value of $user
     */ 
    public function getMember()
    {
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
    private $leader = null;
    
    /**
     * the $leader getter
     *
     * @return the value of $leader
     */ 
    public function getLeader()
    {
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
    private $sheet = null;
    
    /**
     * the $sheet getter
     *
     * @return the value of $sheet
     */ 
    public function getSheet()
    {
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
     * Creates an Invitation object, for database post(insert) and put(update).
     * Not needed attributes can be set to null.
     *
     * @param string $leaderId The id of the leader.
     * @param string $memberId The id of a member.
     * @param string $sheetId The id of the exercise sheet.
     *
     * @return an invitation object
     */
    public static function createInvitation($leaderId,$memberId,$sheetId)
    {
        return new Invitation(array('sheet' => $sheetId,
        'leader' => User::createUser($leaderId,null,null,null,null,
                               null,null,null,null,null), 
        'member' => User::createUser($memberId,null,null,null,null,
                               null,null,null,null,null)));
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
    public function getInsertData()
    {
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
        if ($data==null)
            $data = array();
        
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
        if ($decode && $data==null) 
            $data = "{}";
    
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
        $list = array();
        if ($this->member!==null) $list['member'] = $this->member;
        if ($this->leader!==null) $list['leader'] = $this->leader;
        if ($this->sheet!==null) $list['sheet'] = $this->sheet;
        return $list;  
    }
    
    public static function ExtractInvitation($data, $singleResult = false)
    {
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $leader = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert());
            
            // generates an assoc array of users by using a defined list of 
            // its attributes
            $member = DBJson::getObjectsByAttributes($data, 
                                            User::getDBPrimaryKey(), 
                                            User::getDBConvert(), 
                                            '2');
                                            
            // generates an assoc array of invitations by using a defined list of 
            // its attributes
            $invitations = DBJson::getObjectsByAttributes($data, 
                                    Invitation::getDBPrimaryKey(), 
                                    Invitation::getDBConvert());  
                                    
            // concatenates the invitations and the associated invitation leader
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $invitations,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_leader'] ,
                            $leader,
                            User::getDBPrimaryKey());
       
            // concatenates the invitations and the associated invitation member
            $res = DBJson::concatObjectListsSingleResult($data, 
                            $res,
                            Invitation::getDBPrimaryKey(),
                            Invitation::getDBConvert()['U_member'] ,
                            $member,
                            User::getDBPrimaryKey(),
                            '2');
                            
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