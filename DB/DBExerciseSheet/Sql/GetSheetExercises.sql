select 
    E.ES_id,
    E.E_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link
from
    Exercise E
where
    E.ES_id = '$esid'