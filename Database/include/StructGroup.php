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
    private $_members;
    public function getMembers(){
        return $this->_members;
    }
    public function setMembers($_value){
        $this->_members = $_value;
    }

    /**
     * the id of the user that is the leader of the group
     *
     * type: User
     */
    private $_leader;
    public function getLeader(){
        return $this->_leader;
    }
    public function setLeader($_value){
        $this->_leader = $_value;
    }

    /**
     * the id of the sheet for which this group exists
     *
     * type: string
     */
    private $_sheetId;
    public function getSheetId(){
        return $this->_sheetId;
    }
    public function setSheetId($_value){
        $this->_sheetId = $_value;
    }
    
    
    public static function getDBConvert(){
        return array(
           'U_id_member' => '_members',
           'U_id_leader' => '_leaderId',
           'ES_id' => '_sheetId',
        );
    }
    
    public static function getDBPrimaryKey(){
        return array('C_id');
    }
   
   
    public function __construct($_data=array()) {
        foreach ($_data AS $_key => $_value) {
             if (isset($_key)){
                if (is_array($_value)) {
                    $_sub = new User($_value);
                    $_value = $_sub;
                }
                $this->{$_key} = $_value;
            }
        }
    }
    
    public static function encodeGroup($_data){
        return json_encode($_data);
    }
    
    public static function decodeGroup($_data){
        $_data = json_decode($_data);
        if (is_array($_data)){
            $result = array();
            foreach ($_data AS $_key => $_value) {
                array_push($result, new Group($_value));
            }
            return $result;   
        }
        else
            return new Group($_data);
    }
    
    public function jsonSerialize() {
        return array(
            '_members' => $this->_members,
            '_leaderId' => $this->_leader,
            '_sheetId' => $this->_sheetId
        );
    }
}
?>