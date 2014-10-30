<?php
/**
 * @file GetAllFiles.sql
 * gets all files from %File table
 * @author Till Uhlig
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
<?php if (isset($begin) && $begin!='' && isset($end) && $end!=''){ ?>
    where
    <?php if (isset($begin) && $begin!=''){ ?>
        F_timeStamp>='<?php echo $begin; ?>' 
    <?php } ?>

    <?php if (isset($begin) && $begin!='' && isset($end) && $end!=''){ ?>
        and 
    <?php } ?>

    <?php if (isset($end) && $end!=''){ ?>
        F_timeStamp<='<?php echo $end; ?>'
    <?php } ?>

<?php } ?>