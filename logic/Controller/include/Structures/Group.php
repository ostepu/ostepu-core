<?php 
/**
 * 
 */
class Group extends Object implements JsonSerializable
{
    /**
     * all members of the group of the current users
     * 
     * type: User[]
     */
    private $members;
    public function getMembers(){
        return $this->members;
    }
    public function setMembers($value){
        $this->members = $value;
    }

    /**
     * the id of the user that is the leader of the group
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
     * the id of the sheet for which this group exists
     *
     * type: string
     */
    private $sheetId;
    public function getSheetId(){
        return $this->sheetId;
    }
    public function setSheetId($value){
        $this->sheetId = $value;
    }
    
    
    public static function getDBConvert(){
        return array(
           'U_id_member' => 'members',
           'U_id_leader' => 'leaderId',
           'ES_id' => 'sheetId',
        );
    }
    
    public static function getDBPrimaryKey(){
        return array('C_id');
    }
   
   
    public function __construct($data=array()) {
        foreach ($data AS $key => $value) {
             if (isset($key)){
                if (is_array($value)) {
                    $sub = new User($value);
                    $value = $sub;
                }
                $this->{$key} = $value;
            }
        }
    }
    
    public static function encodeGroup($data){
        return json_encode($data);
    }
    
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
    
    public function jsonSerialize() {
        return array(
            'members' => $this->members,
            'leaderId' => $this->leader,
            'sheetId' => $this->sheetId
        );
    }
}
?>