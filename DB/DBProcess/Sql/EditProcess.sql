/**
 * @file EditProcess.sql
 * updates a specified process from %Process table
 * @author  Till Uhlig
 * @param int \$processid a %Process identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
 
UPDATE `Process_".Process::getCourseFromProcessId($processid)."`
SET {$object->getInsertData()}
WHERE PRO_id = '".Process::getIdFromProcessId($processid)."'