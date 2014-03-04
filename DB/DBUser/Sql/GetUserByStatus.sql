/**
 * @file GetUserByStatus.sql
 * gets all specified user from %User table
 * @author Till Uhlig
 * @param int \$statusid a course status identifier (0-4)
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
 
SELECT 
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U.U_password,
    U.U_salt,
    U.U_failed_logins,
    U.U_externalId,
    U.U_studentNumber,
    U.U_isSuperAdmin,
    U.U_comment,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize
FROM
    User U
        left join
    CourseStatus CS ON (U.U_id = CS.U_id)
        left join
    Course C ON (CS.C_id = C.C_id)
where CS.CS_status = '$statusid'