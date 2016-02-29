<?php
/**
 * @file EditExercise.sql
 * updates an specified exercise from %Exercise table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$eid an %Exercise identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Exercise
SET <?php echo $values; ?>
WHERE E_id = '<?php echo $eid; ?>'