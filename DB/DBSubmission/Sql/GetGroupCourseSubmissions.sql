<?php
/**
 * @file GetGroupSelectedCourseSubmissions.sql
 * gets the specified selected submissions from %Submission table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @param int \$userid a %User identifier
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
    F.F_mimeType,
    S.U_id,
    S.S_id,
    S.F_id_file,
    S.S_comment,
    S.S_date,
    SS.S_id_selected as S_selected,
    S.S_accepted,
    S.S_flag,
    S.S_leaderId,
    S.S_hideFile,
    S.E_id,
    S.ES_id
from
    `Group` G
        join
    `Group` G2 ON (G.U_id_leader = '<?php echo $userid; ?>'
        and G.U_id_member = G2.U_id_member
        and G.C_id = '<?php echo $courseid; ?>'
        and G2.ES_id = G.ES_id)
        join
    (Submission S
    join Exercise E ON (S.E_id = E.E_id and E.C_id = '<?php echo $courseid; ?>')) ON (G2.U_id_leader = S.U_id)
        left join
    File F ON (S.F_id_file = F.F_id)
        left join
    SelectedSubmission SS ON (S.S_id = SS.S_id_selected
        and S.E_id = SS.E_id)

