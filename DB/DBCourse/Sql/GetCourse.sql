select 
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    ES.ES_id
from
    Course C
        left join
    ExerciseSheet ES ON C.C_id = ES.C_id
where
    C.C_id = '$courseid'