/**
 * @file AddChoice.sql
 * inserts a choice into %Choice table
 * @author  Till Uhlig
 * @result -
 */
 
SET @course = '".Form::getCourseFromFormId($object->getFormId())."';
SET @statement = 
concat(
\"INSERT INTO `Choice_\", @course, \"` SET {$object->getInsertData()};\");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';