<?php
/** 
 * @file DeleteExerciseExerciseFileType.sql
 * deletes specified exercise file types from %ExerciseFileType table
 * @author  Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType 
WHERE
    E_id = '<?php echo $eid; ?>';