/**
 * @file AddExerciseTransaction.sql
 * inserts an transaction into %Transaction table
 * @author  Till Uhlig
 * @result -
 */

SET @course = (select E.C_id from `Exercise` E where E.E_id = {$eid} limit 1);
SET @statement = 
concat(\"INSERT INTO `Transaction{$name}_\", @course, \"` SET {$object->getInsertData()},T_random = '{$random}';\");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';