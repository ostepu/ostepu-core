<?php 
/**
 * Assigns certain exercises with to tutor.
 */
class TutorAssignment extends Object implements JsonSerializable
{
    /**
     * tutor
     *
     * type: User
     */
    private $_tutor;
    public function getTutor(){
        return $this->_tutor;
    }
    public function setTutor($_value){
        $this->_tutor = $_value;
    }

    /**
     * ids of the submissions the tutor was assigned to correct.
     *
     * type: string[]
     */
    private $_submissionIds = array();
    public function getSubmissionIds(){
        return $_submissionIds;
    }
    public function setSubmissionIds($_value){
        $_submissionIds = $_value;
    }

    public function jsonSerialize() {
        return array(
            '_tutor' => $this->_tutor,
            '_submissionIds' => $_submissionIds
        );
    }
}
?>