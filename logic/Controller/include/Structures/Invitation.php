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
    
    public function jsonSerialize() {
        return array(
            'user' => $this->user,
            'leader' => $this->leader,
            'sheet' => $this->sheet
        );
    }
}
?>