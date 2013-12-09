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
    private $_user;
    public function getUser(){
        return $this->_user;
    }
    public function setUser($_value){
        $this->_user = $_value;
    }

    /**
     * the user that created the group
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
     * 
     * type: string
     */
    private $_sheet;
    public function getSheet(){
        return $this->_sheet;
    }
    public function setSheet($_value){
        $this->_sheet = $_value;
    }
    
    public function jsonSerialize() {
        return array(
            '_user' => $this->_user,
            '_leader' => $this->_leader,
            '_sheet' => $this->_sheet
        );
    }
}
?>