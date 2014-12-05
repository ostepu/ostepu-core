<?php
/**
 * @file GetUser.sql
 * gets an specified User from %User table
 * @author Till Uhlig
 * @param string \$userid a %User identifier
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
?>
CALL `DBUserGetUser`('<?php echo $userid; ?>');