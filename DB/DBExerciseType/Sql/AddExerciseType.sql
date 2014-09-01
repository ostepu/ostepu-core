<?php
/**
 * @file AddExerciseType.sql
 * inserts a possible type into %ExerciseType table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
INSERT INTO ExerciseType SET <?php echo $values; ?>