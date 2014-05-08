/**
 * @file AddAttachment.sql
 * inserts an attachment into %Attachment table
 * @author  Till Uhlig
 * @result -
 */
 
SET @course = (select E.C_id from `Exercise` E where E.E_id = {$object->getExerciseId()} limit 1);
SET @statement = 
concat(\"INSERT INTO `Attachment_{$pre}_\", @course, \"` SET {$object->getInsertData()};\");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';