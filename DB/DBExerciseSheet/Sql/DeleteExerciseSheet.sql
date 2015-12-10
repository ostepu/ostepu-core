<?php
/**
 * @file DeleteExerciseSheet.sql
 * deletes an specified exercise sheet from %ExerciseSheet table
 * @author  Till Uhlig
 * @param int \$esid an %ExerciseSheet identifier
 * @result -
 */
?>

DELETE FROM ExerciseSheet
WHERE
    ES_id = '<?php echo $esid; ?>'