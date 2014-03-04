/**
 * @file GetExerciseSheetURL.sql
 * gets a exercise sheet file
 * @author Till Uhlig
 * @param int \$esid a %ExerciseSheet identifier
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
    ES.ES_id = '$esid'