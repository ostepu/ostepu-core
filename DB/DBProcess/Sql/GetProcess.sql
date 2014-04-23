/**
 * @file GetProcess.sql
 * gets a process from %Process table
 * @author Till Uhlig
 * @param int \$processid an %Process identifier
 * @result 
 * - PRO, the process data
 * - CO, the component data
 */
 
SET @course = '".Process::getCourseFromProcessId($processid)."';
SET @statement = 
concat(
\"select 
    concat('\", @course ,\"','_',PRO.PRO_id) as PRO_id,
    PRO.E_id,
    PRO.PRO_parameter,
    CO.CO_id,
    CO.CO_name,
    CO.CO_address
from
    `Process_\", @course, \"` PRO
        left join
    `Component` CO ON PRO.CO_id_target = CO.CO_id
where
    PRO.PRO_id = '".Process::getIdFromProcessId($processid)."'\");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;