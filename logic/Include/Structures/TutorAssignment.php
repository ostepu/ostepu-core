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
    private $tutor;
    public function getTutor(){
        return $this->tutor;
    }
    public function setTutor($value){
        $this->tutor = $value;
    }

    /**
     * ids of the submissions the tutor was assigned to correct.
     *
     * type: string[]
     */
    private $submissionIds = array();
    public function getSubmissionIds(){
        return $submissionIds;
    }
    public function setSubmissionIds($value){
        $submissionIds = $value;
    }
    
    /**
     * the json serialize function
     */
    public function jsonSerialize()
    {
        return array(
            'tutor' => $this->tutor,
            'submissionIds' => $submissionIds
        );
    }
}
?>