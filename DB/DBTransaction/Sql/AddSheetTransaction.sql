/**
 * @file AddSheetTransaction.sql
 * inserts an transaction into %Transaction table
 * @author  Till Uhlig
 * @result -
 */

SET @course = (select ES.C_id from `ExerciseSheet` ES where ES.ES_id = {$esid} limit 1);
SET @statement = 
concat(\"INSERT INTO `Transaction{$name}_\", @course, \"` SET {$object->getInsertData()},T_random = '{$random}';\");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';