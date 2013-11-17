<?php 

include_once 'include/Helpers.php';
/**
* 
*/
class ExerciseSheet
{
    private $sheetName;
    private $exercises;
    private $endTime;
    private $percent; 

    public function __construct($sheetName, $exercises, $percent, $endTime)
    {
        $this->sheetName = $sheetName;
        $this->exercises = $exercises;
        $this->percent = $percent;
        $this->endTime = $endTime;
    }

    public function show()
    {
        $exerciseTemplate = file_get_contents('include/ExerciseSheet/Exercise.template.html');
        $content = file_get_contents('include/ExerciseSheet/ExerciseSheet.template.html');

        $exerciseHTML = '';
        foreach ($this->exercises as $exercise) {
            $thisExercise = str_replace('%exerciseType%',
                                        $exercise['exerciseType'],
                                        $exerciseTemplate);
            $thisExercise = str_replace('%points%',
                                        $exercise['points'],
                                        $thisExercise);
            $thisExercise = str_replace('%maxPoints%', 
                                        $exercise['maxPoints'],
                                        $thisExercise);
            $exerciseHTML .= "{$thisExercise}\n";
        }

        $content = str_replace('%sheetName%',
                               $this->sheetName,
                               $content);

        $content = str_replace('%percent%',
                               $this->percent,
                               $content);

        $content = str_replace('%endTime%',
                               $this->endTime,
                               $content);

        $content = str_replace('%exercises%',
                               $exerciseHTML,
                               $content);

        print $content;
    }
}
?>