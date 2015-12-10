<?php
/**
 * @file AddAttachment.sql
 * inserts an attachment into %Attachment table
 * @author  Till Uhlig
 * @result -
 */
?>

SET @course = (select E.C_id from `Exercise` E where E.E_id = <?php echo $object->getExerciseId(); ?> limit 1);
SET @statement = 
concat("INSERT INTO `Attachment<?php echo $pre; ?>_", @course, "` SET <?php echo $object->getInsertData(true); ?>;");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';