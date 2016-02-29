<?php
/**
 * @file DeleteExerciseFileType.sql
 * deletes a specified exercise file type from %ExerciseFileType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$eftid a %ExerciseFileType identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType
WHERE
    EFT_id = '<?php echo $eftid; ?>'