<?php
/**
 * @file DeleteExerciseType.sql
 * deletes a specified possible type from %ExerciseType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 * @param int \$etid a %ExerciseType identifier
 * @result -
 */
?>

DELETE FROM ExerciseType
WHERE
    ET_id = '<?php echo $etid; ?>'