<?php
/**
 * @file EditExerciseType.sql
 * updates an specified possible type from %ExerciseType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @param int \$etid a %ExerciseType identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseType
SET <?php echo $values; ?>
WHERE ET_id = '<?php echo $etid; ?>'