/**
 * @file GetCourseSheets.sql
 * gets all course exercise sheets
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result 
 * - ES, the exercise sheet data
 * - F, the exercise sheet file
 * - F2, the sample solution file
 */
 
select 
    ES.ES_id,
    ES.C_id,
    ES.ES_endDate,
    ES.ES_startDate,
    ES.ES_groupSize,
    ES.ES_name,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_hash,
    F2.F_id as F_id2,
    F2.F_displayName as F_displayName2,
    F2.F_address as F_address2,
    F2.F_timeStamp as F_timeStamp2,
    F2.F_fileSize as F_fileSize2,
    F2.F_hash as F_hash2
from
    (ExerciseSheet ES
    left join File F ON F.F_id = ES.F_id_file)
        left join
    File F2 ON (F2.F_id = ES.F_id_sampleSolution)
where
    ES.C_id = '$courseid'