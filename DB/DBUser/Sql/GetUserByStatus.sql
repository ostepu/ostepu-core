<?php
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
?>
CALL `DBUserGetUserByStatus`('<?php echo $statusid; ?>');