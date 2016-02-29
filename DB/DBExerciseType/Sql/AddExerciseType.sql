<?php
/**
 * @file AddExerciseType.sql
 * inserts a possible type into %ExerciseType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO ExerciseType SET <?php echo $values; ?>