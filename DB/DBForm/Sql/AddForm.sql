/**
 * @file AddForm.sql
 * inserts a form into %Form table
 * @author  Till Uhlig
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */

SET @course = (select E.C_id from `Exercise` E where E.E_id = {$object->getExerciseId()} limit 1);
SET @statement = 
concat(\"INSERT INTO `Form_\", @course, \"` SET {$object->getInsertData()};\");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';