<?php
/**
 * @file AddExerciseFileType.sql
 * inserts a exercise file type into %ExerciseFileType table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
INSERT INTO ExerciseFileType SET <?php echo $values; ?>