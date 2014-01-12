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
    
    /**
     * the $tutor getter
     *
     * @return the value of $tutor
     */ 
    public function getTutor(){
        return $this->tutor;
    }
    
    /**
     * the $tutor setter
     *
     * @param string $value the new value for $tutor
     */ 
    public function setTutor($value){
        $this->tutor = $value;
    }

    /**
     * ids of the submissions the tutor was assigned to correct.
     *
     * type: string[]
     */
    private $submissionIds = array();
    
    /**
     * the $submissionIds getter
     *
     * @return the value of $submissionIds
     */ 
    public function getSubmissionIds(){
        return $submissionIds;
    }
    
    /**
     * the $submissionIds setter
     *
     * @param string $value the new value for $submissionIds
     */ 
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