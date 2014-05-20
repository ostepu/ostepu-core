/** 
 * @file DeleteProcess.sql
 * deletes a specified process from %Process table
 * @author  Till Uhlig
 * @param int \$processid a %Process identifier
 * @result -
 */

DELETE FROM `Process{$pre}_".Process::getCourseFromProcessId($processid)."`
WHERE
    PRO_id = '".Process::getIdFromProcessId($processid)."'