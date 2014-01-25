/**
 * @file GetExerciseMarkings.sql
 * gets all specified markings from %Marking table
 * @author Till Uhlig
 * @param int \$eid an %Exercise identifier
 * @result 
 * - M, the marking data
 * - F, the marking file
 * - S, the submission data
 * - SS, the selected data
 */
 
SELECT 
    M.M_id,
    M.U_id_tutor,
    M.S_id,
    M.M_tutorComment,
    M.M_outstanding,
    M.M_status,
    M.M_points,
    M.M_date,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_hash,
    S.U_id as U_id2,
    S.S_id as S_id2,
    S.F_id_file as F_id_file2,
    S.S_comment as S_comment2,
    S.S_date as S_date2,
    SS.S_id_selected as S_selected2,
    S.S_accepted as S_accepted2,
    S.S_flag as S_flag2,   
    S.E_id as E_id2
from
    Marking M
        join
    SelectedSubmission SS ON (M.S_id = SS.S_id_selected)
        join
    Submission S ON (M.S_id = S.S_id)
        join
    File F ON (F.F_id = M.F_id_file)
where
    M.E_id = '$eid'