/** 
 * @file DeleteProcess.sql
 * deletes a specified process from %Process table
 * @author  Till Uhlig
 * @param int \$processid a %Process identifier
 * @result -
 */

DELETE FROM `Process_".Process::getCourseFromProcessId($processid)."`
WHERE
    PRO_id = '".Process::getIdFromProcessId($processid)."'