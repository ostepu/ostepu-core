<?php
/**
 * @file DeleteProcess.sql
 * deletes a specified process from %Process table
 * @author  Till Uhlig
 * @param int \$processid a %Process identifier
 * @result -
 */
?>

DELETE FROM `Process<?php echo $pre; ?>_<?php echo Process::getCourseFromProcessId($processid); ?>`
WHERE
    PRO_id = '<?php echo Process::getIdFromProcessId($processid); ?>'