<?php
/**
 * @file EditExerciseSheet.sql
 * updates an specified exercise sheet from %ExerciseSheet table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$esid an %ExerciseSheet identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseSheet
SET <?php echo $values; ?>
WHERE ES_id = '<?php echo $esid; ?>'