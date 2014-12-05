<?php
/**
 * @file GetCourseUserByStatus.sql
 * gets all specified course users from %User table
 * @author Till Uhlig
 * @param int \$statusid a course status identifier (0-4)
 * @param int \$courseid a %Course identifier (0-4)
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
?>
CALL `DBUserGetCourseUserByStatus`('<?php echo $courseid; ?>','<?php echo $statusid; ?>');