<?php
/**
 * @file GetFileByHash.sql
 * gets a specified file from %File table
 * @author Till Uhlig
 * @param string \$hash a %File hash
 * @result F_id, F_displayName, F_address, F_timeStamp, F_fileSize, F_hash
 */
?>
 
select 
    F_id,
    F_displayName,
    F_address,
    F_timeStamp,
    F_fileSize,
    F_comment,
    F_hash,
    F_mimeType
from
    File
where
    F_hash = '<?php echo $hash; ?>'