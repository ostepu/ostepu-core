select 
    A.A_id,
    A.E_id,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_hash
from
    Attachment A
        left join
    File F ON F.F_id = A.F_id
where
    A.A_id = '$aid'