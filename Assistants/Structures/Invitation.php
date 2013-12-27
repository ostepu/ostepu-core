<?php
/**
* 
*/
class Invitation extends Object implements JsonSerializable
{
    /**
     * the user that was invited
     * 
     * type: User
     */
    private $user;
    public function getUser(){
        return $this->user;
    }
    public function setUser($value){
        $this->user = $value;
    }

    /**
     * the user that created the group
     * 
     * type: User 
     */
    private $leader;
    public function getLeader(){
        return $this->leader;
    }
    public function setLeader($value){
        $this->leader = $value;
    }

    /**
     * 
     * type: string
     */
    private $sheet;
    public function getSheet(){
        return $this->sheet;
    }
    public function setSheet($value){
        $this->sheet = $value;
    }
    
    
    
    
    
    /**
     * (description)
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
     * (description)
     */
    public function getInsertData(){
        $values = "";
        
        if ($this->sheet != null) $this->addInsertData($values, 'ES_id', $this->sheet );
        if ($this->member != null) $this->addInsertData($values, 'U_id_member', $this->member->getId());
        if ($this->leader != null) $this->addInsertData($values, 'U_id_leader', $this->leader->getId());
        
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
        return 'U_id_leader';
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
                if ($key == 'user' || $key == 'leader') {
                    $this->{$key} = User::decodeUser($value, false);
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
    public static function encodeInvitation($data)
    {
        return json_encode($data);
    }
    
    /**
     * (description)
     * 
     * @param $param (description)
     * @param $param (description)
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
     * (description)
     */
    public function jsonSerialize() {
        return array(
            'user' => $this->user,
            'leader' => $this->leader,
            'sheet' => $this->sheet
        );
    }
}
?>