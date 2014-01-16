select 
    E.E_id,
    E.ES_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link,
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
    S.E_id as E_id2
from
    Exercise E
        left join
    (Attachment A
    natural join File F) ON E.E_id = A.E_id
        left join
    (Submission S
    join SelectedSubmission SS ON S.S_id = SS.S_id_selected) ON S.E_id = E.E_id
where
    E.E_id = '$eid'