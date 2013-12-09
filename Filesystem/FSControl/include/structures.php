<?php
// TODO: Passwortverwaltung
// TODO: Verschluesselung

// Fragen
// * Klasse für Punktearten in JSON?                                  gute Frage

//if (file_exists('StructBackup.php'))
    include 'StructBackup.php';
    
//if (file_exists('StructComponent.php'))
    include 'StructComponent.php';

//if (file_exists('StructCourse.php'))
    include 'StructCourse.php';

//if (file_exists('StructCourseStatus.php'))
    include 'StructCourseStatus.php';

//if (file_exists('StructExercise.php'))
    include 'StructExercise.php';

//if (file_exists('StructExerciseSheet.php'))
    include 'StructExerciseSheet.php';

//if (file_exists('StructFile.php'))
    include 'StructFile.php';

//if (file_exists('StructGroup.php'))
    include 'StructGroup.php';

//if (file_exists('StructInvitation.php'))
    include 'StructInvitation.php';

//if (file_exists('StructLink.php'))
    include 'StructLink.php';

//if (file_exists('StructMarking.php'))
    include 'StructMarking.php';

//if (file_exists('StructSubmission.php'))
    include 'StructSubmission.php';

//if (file_exists('StructTutorAssignment.php'))
    include 'StructTutorAssignment.php';

//if (file_exists('StructUser.php'))
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