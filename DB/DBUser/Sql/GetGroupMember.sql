<?php
/**
 * @file GetUser.sql
 * gets all specified user from %User table
 * @author Till Uhlig
 * @param int \$userid a %User identifier (0-4)
 * @param int \$esid a %ExerciseSheet identifier (0-4)
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
?>
CALL `DBUserGetGroupMember`('<?php echo $esid; ?>','<?php echo $userid; ?>');