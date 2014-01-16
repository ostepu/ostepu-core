select 
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition
where
    C_id = '$courseid'