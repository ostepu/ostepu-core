<?php
/** 
 * @file DeleteExercise.sql
 * deletes an specified exercise from %Exercise table
 * @author  Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE
FROM Exercise
WHERE E_id = '<?php echo $eid; ?>';