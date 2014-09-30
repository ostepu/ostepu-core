<?php
/**
 * @file GetAllSessions.sql
 * gets all sessions from %Session table
 * @author Till Uhlig
 * @result  U_id, SE_id
 */
?>
 
select 
    U_id,
    SE_sessionID
from
    `Session`