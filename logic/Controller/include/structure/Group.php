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
    
    public function jsonSerialize() {
        return array(
            '_members' => $this->_members,
            '_leaderId' => $this->_leader,
            '_sheetId' => $this->_sheetId
        );
    }
}
?>