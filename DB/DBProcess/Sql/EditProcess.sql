<?php
/**
 * @file EditProcess.sql
 * updates a specified process from %Process table
 * @author  Till Uhlig
 * @param int \$processid a %Process identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE `Process<?php echo $pre; ?>_<?php echo Process::getCourseFromProcessId($processid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE PRO_id = '<?php echo Process::getIdFromProcessId($processid); ?>'