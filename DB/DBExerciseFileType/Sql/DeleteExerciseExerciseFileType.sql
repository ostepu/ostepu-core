<?php
/**
 * @file DeleteExerciseExerciseFileType.sql
 * deletes specified exercise file types from %ExerciseFileType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType
WHERE
    E_id = '<?php echo $eid; ?>';