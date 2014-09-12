<?php
/**
 * @file GetAllSubmissions.sql
 * gets all submissions from %Submission table
 * @author Till Uhlig
 * @result 
 * - F, the submission file
 * - S, the submission data
 * - SS, the selected data
 */
?>
 
select 
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    S.U_id,
    S.S_id,
    S.F_id_file,
    S.S_comment,
    S.S_date,
    SS.S_id_selected,
    S.S_accepted,
    S.S_flag,
    S.S_leaderId,
    S.S_hideFile,
    S.E_id,
    S.ES_id
from
    Submission S
    join File F on (S.F_id_file = F.F_id)
left join SelectedSubmission SS on (S.S_id = SS.S_id_selected)
