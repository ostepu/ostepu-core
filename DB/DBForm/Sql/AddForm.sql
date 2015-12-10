<?php
/**
 * @file AddForm.sql
 * inserts a form into %Form table
 * @author  Till Uhlig
 * @result -
 */
?>

SET @course = (select E.C_id from `Exercise` E where E.E_id = <?php echo $object->getExerciseId(); ?> limit 1);
SET @statement =
concat("INSERT INTO `Form_", @course, "` SET <?php echo $object->getInsertData(true); ?>;");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';