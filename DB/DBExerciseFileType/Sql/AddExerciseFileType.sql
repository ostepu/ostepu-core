<?php
/**
 * @file AddExerciseFileType.sql
 * inserts a exercise file type into %ExerciseFileType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO ExerciseFileType SET <?php echo $values; ?>