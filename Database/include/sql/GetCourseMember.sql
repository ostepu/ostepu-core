select U_id as _Id, U_username as _userName, U_email as _email, U_firstName as _firstName, U_lastName as _lastName, U_title as _title, NULL as _courses
from user natural join coursestatus
where C_id = $courseid