/**
 * @file GetCourseSheetsURLs.sql
 * gets all course exercise sheets files
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result 
 * - F, the exercise sheet file
 */
 
select 
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash
from
    ExerciseSheet ES
        left join
    File F ON F.F_id = ES.F_id_file
where
    ES.C_id = '$courseid'