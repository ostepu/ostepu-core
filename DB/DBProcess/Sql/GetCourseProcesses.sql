/**
 * @file GetCourseProcesses.sql
 * gets all processes of a course from %Process table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - PRO, the process data
 * - CO, the component data
 */
 
select 
    concat('{$courseid}','_',PRO.PRO_id) as PRO_id,
    PRO.E_id,
    PRO.PRO_parameter,
    PRO.CO_id_target,
    CO.CO_id,
    CO.CO_name,
    CO.CO_address
from
    `Process_{$courseid}` PRO
        left join
    `Component` CO ON PRO.CO_id_target = CO.CO_id