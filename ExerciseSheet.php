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
        $content = getIncludeContents('ExerciseSheet.template.html');

        $content = str_replace('%sheetName%',
                               $this->sheetName,
                               $content);

        print $content;
    }
}
?>