<?php
/**
 * @file DeleteExerciseSheet.sql
 * deletes an specified exercise sheet from %ExerciseSheet table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$esid an %ExerciseSheet identifier
 * @result -
 */
?>

DELETE FROM ExerciseSheet
WHERE
    ES_id = '<?php echo $esid; ?>'