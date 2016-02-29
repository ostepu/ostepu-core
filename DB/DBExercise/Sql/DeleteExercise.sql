<?php
/**
 * @file DeleteExercise.sql
 * deletes an specified exercise from %Exercise table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE
FROM Exercise
WHERE E_id = '<?php echo $eid; ?>';