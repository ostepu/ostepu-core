select 
    ES.ES_id,
    E.E_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link
from
    ExerciseSheet ES
        left join
    Exercise E ON (E.ES_id = ES.ES_id)
where
    ES.C_id = '$courseid'