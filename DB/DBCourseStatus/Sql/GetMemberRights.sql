<?php
/**
 * @file GetMemberRights.sql
 * gets the course status for a user 
 * @author Till Uhlig
 * @param int \$userid an %User identifier
 * @result 
 * - U, the user data
 * - C, the course data
 * - CS, the courstatus data
 */
?>
CALL `DBCourseStatusGetMemberRights`('<?php echo $userid; ?>');