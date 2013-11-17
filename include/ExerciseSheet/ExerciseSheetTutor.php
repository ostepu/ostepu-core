<?php 

include_once 'include/Helpers.php';
/**
* 
*/
class ExerciseSheet
{
    private $sheetName;
    private $exercises;

    public function __construct($sheetName, $exercises)
    {
        $this->sheetName = $sheetName;
        $this->exercises = $exercises;
    }

    public function show()
    {
        $exerciseTemplate = file_get_contents('include/ExerciseSheet/ExerciseTutor.template.html');
        $content = file_get_contents('include/ExerciseSheet/ExerciseSheetTutor.template.html');

        $exerciseHTML = "";
        foreach ($this->exercises as $exercise) {
            $thisExercise = str_replace('%exerciseType%',
                                        $exercise["exerciseType"],
                                        $exerciseTemplate);
            $thisExercise = str_replace('%maxPoints%', 
                                        $exercise["maxPoints"],
                                        $thisExercise);
            $exerciseHTML .= "{$thisExercise}\n";
        }

        $content = str_replace('%sheetName%',
                               $this->sheetName,
                               $content);

        $content = str_replace('%exercises%',
                               $exerciseHTML,
                               $content);

        print $content;
    }
}
?>