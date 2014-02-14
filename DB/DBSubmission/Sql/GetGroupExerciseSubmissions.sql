/**
 * @file GetGroupExerciseSubmissions.sql
 * gets the specified submissions from %Submission table
 * @author Till Uhlig
 * @param int \$eid an %Exercise identifier
 * @param int \$userid a %User identifier
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
    F.F_comment,
    F.F_hash,
    S.U_id,
    S.S_id,
    S.F_id_file,
    S.S_comment,
    S.S_date,
    SS.S_id_selected as S_selected,
    S.S_accepted,
    S.S_flag,
    S.S_leaderId,
    S.E_id
from
   (Submission S
    join File F ON (S.F_id_file = F.F_id
        and S.E_id = '$eid')
    left join SelectedSubmission SS ON (S.S_id = SS.S_id_selected
        and S.E_id = SS.E_id))
        join


(`Group` G
        join
    `Group` G2 ON (G.U_id_leader = '$userid'
        and G.U_id_member = G2.U_id_member
        and G2.ES_id = G.ES_id)
)
         ON (G.ES_id = S.ES_id and S.U_id = G2.U_id_leader)
