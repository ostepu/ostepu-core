<?php
// TODO: Passwortverwaltung
// TODO: Verschluesselung

// Fragen
// * Klasse für Punktearten in JSON?                                    gute Frage

include 'structure/Backup.php';
include 'structure/Component.php';
include 'structure/Course.php';
include 'structure/CourseStatus.php';
include 'structure/Exercise.php';
include 'structure/ExerciseSheet.php';
include 'structure/File.php';
include 'structure/Group.php';
include 'structure/Invitation.php';
include 'structure/Link.php';
include 'structure/Marking.php';
include 'structure/Submission.php';
include 'structure/TutorAssignment.php';
include 'structure/User.php';

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
    private $_sender;
	public function getSender(){
	    return $this->_sender;
	}
	public function setSender($_value){
	    $this->_sender = $_value;
	}
}

?>