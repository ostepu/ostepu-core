<?php
// TODO: Passwortverwaltung
// TODO: Verschluesselung

// Fragen
// * Klasse für Punktearten in JSON?                                  gute Frage

include 'StructBackup.php';
include 'StructComponent.php';
include 'StructCourse.php';
include 'StructCourseStatus.php';
include 'StructExercise.php';
include 'StructExerciseSheet.php';
include 'StructFile.php';
include 'StructGroup.php';
include 'StructInvitation.php';
include 'StructLink.php';
include 'StructMarking.php';
include 'StructSubmission.php';
include 'StructTutorAssignment.php';
include 'StructUser.php';

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