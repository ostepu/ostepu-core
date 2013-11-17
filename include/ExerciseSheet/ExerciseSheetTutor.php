<?php 

include_once 'include/ExerciseSheet/ExerciseSheet.php';

/**
* 
*/
class ExerciseSheetTutor extends ExerciseSheet
{
    public function show()
    {   
        $this->contentTemplate = file_get_contents('include/ExerciseSheet/ExerciseTutor.template.html');
        $this->content = file_get_contents('include/ExerciseSheet/ExerciseSheetTutor.template.html');

        parent::show();
    }
}
?>