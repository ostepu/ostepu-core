<?php
/**
 * @file GetAttachment.sql
 * gets an specified attachment from %Attachment table
 * @author Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result 
 * - A, the attachment data
 * - F, the attachment file
 */
?>

select 
    concat('<?php echo Attachment::getCourseFromAttachmentId($aid); ?>','_',A.A_id) as A_id,
    concat('<?php echo Attachment::getCourseFromAttachmentId($aid); ?>','_',A.PRO_id) as PRO_id,
    A.E_id,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    F.F_mimeType
from
    Attachment<?php echo $pre; ?>_<?php echo Attachment::getCourseFromAttachmentId($aid); ?> A
        left join
    File F ON F.F_id = A.F_id
where
    A.A_id = '<?php echo Attachment::getIdFromAttachmentId($aid); ?>'

    