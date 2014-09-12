<?php
/**
 * @file GetCourseRight.sql
 * gets the course status for a course and a member
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @param int \$userid an %User identifier
 * @result 
 * - U, the user data
 * - C, the course data
 * - CS, the courstatus data
 */
?>
 
 select 
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U.U_studentNumber,
    U.U_isSuperAdmin,
    U.U_comment,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize
from

    CourseStatus CS 
        join
    Course C ON (CS.C_id = C.C_id)
 join
    User U
       ON (U.U_id = CS.U_id)
WHERE
    CS.U_id = '<?php echo $userid; ?>' and CS.C_id = '<?php echo $courseid; ?>'