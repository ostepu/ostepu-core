<?php 

include_once 'include/ExerciseSheet/ExerciseSheet.php';

/**
* 
*/
class ExerciseSheetStudent extends ExerciseSheet
{
    public function show()
    {
        $this->contentTemplate = file_get_contents('include/ExerciseSheet/ExerciseStudent.template.html');
        $this->content = file_get_contents('include/ExerciseSheet/ExerciseSheetStudent.template.html');

        parent::show();

    }
}
?>