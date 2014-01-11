/** 
 * @file RemoveCourseMember.sql
 * deletes an specified course status from %CourseStatus table
 * @author  Till Uhlig
 * @param int $courseid a %Course identifier
 * @param int $userid an %User identifier
 * @result -
 */
 
DELETE FROM CourseStatus 
WHERE
    C_id = '$courseid' and U_id = '$userid'