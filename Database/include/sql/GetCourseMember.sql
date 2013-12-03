select U_id as Id, U_username as userName, U_email as email, U_firstName as firstName, U_lastName as lastName, U_title as title, NULL as courses
from user natural join coursestatus
where C_id = $courseid