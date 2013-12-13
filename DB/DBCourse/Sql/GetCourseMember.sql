select U_id, U_username, U_email, U_firstName, U_lastName, U_title, NULL as U_courses
from user natural join coursestatus
where C_id = $courseid