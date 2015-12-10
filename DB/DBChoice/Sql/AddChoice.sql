<?php
/**
 * @file AddChoice.sql
 * inserts a choice into %Choice table
 * @author  Till Uhlig
 * @result -
 */
?>

SET @course = '<?php echo Form::getCourseFromFormId($object->getFormId()); ?>';
SET @statement =
concat(
"INSERT INTO `Choice<?php echo $preChoice; ?>_", @course, "` SET <?php echo $object->getInsertData(true); ?>;");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';