/**
 * @file GetCourseApprovalConditions.sql
 * gets specified approval conditions from %ApprovalCondition table
 * @author  Till Uhlig
 * @param int $courseid a %Course identifier
 * @result AC_id, C_id, ET_id, AC_percentage
 */

select 
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition
where
    C_id = '$courseid'