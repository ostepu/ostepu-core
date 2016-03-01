<?php
/**
 * @file DeleteExerciseFileType.sql
 * deletes a specified exercise file type from %ExerciseFileType table
 * @author  Till Uhlig
 * @param int \$eftid a %ExerciseFileType identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType
WHERE
    EFT_id = '<?php echo $eftid; ?>'