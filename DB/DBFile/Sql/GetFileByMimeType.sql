<?php
/**
 * @file GetFileByMimeType.sql
 * gets a specified file from %File table
 * @author Till Uhlig
 * @param string \$base a base type (e.g. application, text)
 * @param string \$type a type (e.g. zip, c++)
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
<?php if (!isset($base) || $base=='') { ?>
    F_mimeType is null
<?php } else { ?>
    F_mimeType like '<?php echo $base."/"; echo ((isset($type) && $type!='') ? $type : '%');?>'
<?php } ?>
<?php if (isset($begin) && isset($end)){ ?>
and
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