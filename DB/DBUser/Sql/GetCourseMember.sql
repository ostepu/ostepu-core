<?php
/**
 * @file GetCourseMember.sql
 * gets an specified course member (user)
 * @author Till Uhlig
 * @param int \$courseid a %Course identifier
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
?>
CALL `DBUserGetCourseMember`('<?php echo $courseid; ?>');