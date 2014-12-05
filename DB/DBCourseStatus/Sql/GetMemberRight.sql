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
CALL `DBCourseStatusGetMemberRight`('<?php echo $courseid; ?>', '<?php echo $userid; ?>');