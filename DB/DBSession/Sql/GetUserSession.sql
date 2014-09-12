<?php
/**
 * @file GetUserSession.sql
 * gets the user session from %Session table
 * @author Till Uhlig
 * @param int \$userid a %User identifier
 * @result  U_id, SE_id
 */
?>
 
select 
    U_id,
    SE_sessionID
from
    `Session`
where
    U_id = '<?php echo $userid; ?>'