<?php
/**
 * @file Structures.php contains the Object class and includes a lot of existing api structures
 *
 * @author Till Uhlig
 */ 

    include_once( '/Structures/ApprovalCondition.php' );  
    include_once( '/Structures/Attachment.php' );  
    include_once( '/Structures/Backup.php' );   
    include_once( '/Structures/Component.php' );   
    include_once( '/Structures/Course.php' );   
    include_once( '/Structures/CourseStatus.php' );   
    include_once( '/Structures/Exercise.php' );   
    include_once( '/Structures/ExerciseSheet.php' );  
    include_once( '/Structures/ExerciseType.php' );   
    include_once( '/Structures/ExternalId.php' );     
    include_once( '/Structures/File.php' );   
    include_once( '/Structures/Group.php' );   
    include_once( '/Structures/Invitation.php' );   
    include_once( '/Structures/Link.php' );   
    include_once( '/Structures/Marking.php' );   
    include_once( '/Structures/Query.php' ); 
    include_once( '/Structures/SelectedSubmission.php' );     
    include_once( '/Structures/Session.php' ); 
    include_once( '/Structures/Submission.php' );   
    include_once( '/Structures/TutorAssignment.php' );   
    include_once( '/Structures/User.php' );   

/**
 * the Object class is the parent class of all api structures 
 */
abstract class Object
{
    /**
     * Possibly unnecessary
     * @var string $sender a string that identifies who sent the object
     * @todo what do we do with the "sender" attribute
     *
     * type: string
     */ 
    private $sender;
    
    /**
     * the $sender getter
     *
     * @return the value of $sender
     */
    public function getSender(){
        return $this->sender;
    }
    
    /**
     * the $sender setter
     *
     * @param string $value the new value for $sender
     */
    public function setSender($_value){
        $this->sender = $_value;
    }
    
    /**
     * adds a string comma seperated to another
     *
     * @param string &$a the referenced var, where we have to add the assignment,
     * e.g. addInsertData($a,'a','1') -> $a = ',a=1'
     * @param string $b left part of assignment 
     * @param string $c right part of assignment 
     */  
    protected function addInsertData(&$a, $b, $c){
        $a = $a . ',' . $b . '=\'' . $c . '\'';
    }
}

?>