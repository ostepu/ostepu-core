/**
 * @file GetSessionUser.sql
 * gets the user session from %Session table
 * @author Till Uhlig
 * @param string $seid a %Session identifier
 * @result  U_id, SE_id
 */
 
select 
    U_id,
    SE_id
from
    `Session`
where
    SE_id = '$seid'