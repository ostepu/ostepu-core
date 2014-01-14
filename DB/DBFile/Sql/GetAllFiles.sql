/**
 * @file GetAllFiles.sql
 * gets all files from %File table
 * @author Till Uhlig
 * @result F_id, F_displayName, F_address, F_timeStamp, F_fileSize, F_hash
 */
 
select 
    F_id,
    F_displayName,
    F_address,
    F_timeStamp,
    F_fileSize,
    F_hash
from
    File