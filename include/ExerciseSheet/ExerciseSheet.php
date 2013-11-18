<?php 

include_once 'include/Helpers.php';
/**
* 
*/
class ExerciseSheet
{
    protected $sheetName;
    protected $exercises;
    protected $endTime;
    protected $sheetInfo; 
    protected $content;
    protected $contentTemplate;

    public function __construct($sheetName, $exercises, $sheetInfo, $endTime)
    {
        $this->sheetName = $sheetName;
        $this->exercises = $exercises;
        $this->sheetInfo = $sheetInfo;
        $this->endTime = $endTime;
    }

    public function show()
    {   
        $exerciseHTML = '';
        
        foreach ($this->exercises as $exercise) {
            $thisExercise = str_replace('%exerciseType%',
                                        $exercise['exerciseType'],
                                        $this->contentTemplate);
            $thisExercise = str_replace('%points%',
                                        $exercise['points'],
                                        $thisExercise);
            $thisExercise = str_replace('%maxPoints%', 
                                        $exercise['maxPoints'],
                                        $thisExercise);
            $exerciseHTML .= "{$thisExercise}\n";
        }


        $this->content = str_replace('%sheetName%',
                               $this->sheetName,
                               $this->content);

        $this->content = str_replace('%exerciseSheetInfo%',
                               $this->sheetInfo,
                               $this->content);

        $this->content = str_replace('%endTime%',
                               $this->endTime,
                               $this->content);

        $this->content = str_replace('%exercises%',
                               $exerciseHTML,
                               $this->content);

        print $this->content;
    }
}
?>