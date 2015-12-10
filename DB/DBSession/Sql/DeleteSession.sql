<?php
/** 
 * @file DeleteSession.sql
 * deletes an specified session from %Session table
 * @author  Till Uhlig
 * @param string $seid a %Session identifier
 * @result -
 */
?>

DELETE FROM `Session` 
WHERE
    SE_sessionID = '<?php echo $seid; ?>'