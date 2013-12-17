<?php
// TODO: Passwortverwaltung
// TODO: Verschluesselung

// Fragen
// * Klasse für Punktearten in JSON?                                  gute Frage

    include 'Structures/Backup.php';   
    include 'Structures/Component.php';
    include 'Structures/Course.php';
    include 'Structures/CourseStatus.php';
    include 'Structures/Exercise.php';
    include 'Structures/ExerciseSheet.php';
    include 'Structures/File.php';
    include 'Structures/Group.php';
    include 'Structures/Invitation.php';
    include 'Structures/Link.php';
    include 'Structures/Marking.php';
    include 'Structures/Submission.php';
    include 'Structures/TutorAssignment.php';
    include 'Structures/User.php';
    include 'Structures/Query.php';
    
/**
* 
*/
abstract class Object
{
    /**
     * Possibly unnecessary
     * a string that identifies who sent the object
     *
     * type: string
     */
   /* private $_sender;
    public function getSender(){
        return $this->_sender;
    }
    public function setSender($_value){
        $this->_sender = $_value;
    }*/
}

?>