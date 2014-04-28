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
    PRO.CO_id_target,
    E.ES_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link,
    E.E_linkName,
    CO.CO_id,
    CO.CO_name,
    CO.CO_address
from
    `Process_\", @course, \"` PRO
        left join
    `Component` CO ON PRO.CO_id_target = CO.CO_id)
            join
    `Exercise` E ON (E.E_id = PRO.E_id)
where
    PRO.PRO_id = '".Process::getIdFromProcessId($processid)."'\");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;