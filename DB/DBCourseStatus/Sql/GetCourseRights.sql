<?php
/**
 * @file GetCourseRight.sql
 * gets the course status for a course 
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - U, the user data
 * - C, the course data
 * - CS, the courstatus data
 */
?>
CALL `DBCourseStatusGetCourseRights`('<?php echo $courseid; ?>');