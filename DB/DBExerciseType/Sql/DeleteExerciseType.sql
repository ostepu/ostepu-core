<?php
/** 
 * @file DeleteExerciseType.sql
 * deletes a specified possible type from %ExerciseType table
 * @author  Till Uhlig
 * @param int \$etid a %ExerciseType identifier
 * @result -
 */
?>
 
DELETE FROM ExerciseType 
WHERE
    ET_id = '<?php echo $etid; ?>'