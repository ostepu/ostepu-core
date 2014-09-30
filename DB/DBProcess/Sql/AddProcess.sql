<?php
/**
 * @file AddProcess.sql
 * inserts a process into %Process table
 * @author  Till Uhlig
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

SET @course = <?php echo ($object->getExercise()->getCourseId()!== null ? '\''.$object->getExercise()->getCourseId().'\'' : "(select E.C_id from `Exercise` E where E.E_id = {$object->getExercise()->getId()} limit 1)"); ?>;
SET @statement = 
concat("INSERT INTO `Process<?php echo $pre; ?>_", @course, "` SET <?php echo $object->getInsertData(); ?>;");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';
