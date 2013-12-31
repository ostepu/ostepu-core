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
    F.F_hash
from
    Marking M
        join
    SelectedSubmission S ON (M.S_id = S.S_id_selected)
        join
    Exercise E ON (S.E_id = E.E_id)
        join
    `Group` G ON (G.ES_id = E.ES_id)
        join
    File F ON (F.F_id = M.F_id_file)
where
    E.ES_id = '$esid'
        and G.U_id_leader = '$userid'
        and G.U_id_member = S.U_id_leader