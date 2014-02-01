/**
 * @file GetAllSubmissions.sql
 * gets all submissions from %Submission table
 * @author Till Uhlig
 * @result 
 * - F, the submission file
 * - S, the submission data
 * - SS, the selected data
 */
 
select 
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
    S.S_leaderId as S_leaderId2,
    S.E_id as E_id2
from
    Submission S
    join File F on (S.F_id_file = F.F_id)
left join SelectedSubmission SS on (S.S_id = SS.S_id_selected)
