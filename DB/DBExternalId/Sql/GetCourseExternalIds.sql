/**
 * @file GetCourseExternalIds.sql
 * gets all course external ids from %ExternalId table
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result  C.C_id, C.C_name, C.C_semester, C.C_defaultGroupSize, EX.EX_id
 */
 
 select 
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    EX.EX_id
from
    ExternalId EX left join Course C ON (EX.C_id = C.C_id)
where
    EX.C_id = '$courseid'