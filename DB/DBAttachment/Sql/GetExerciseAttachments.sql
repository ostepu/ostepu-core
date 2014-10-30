<?php
/**
 * @file GetExerciseAttachments.sql
 * gets all exerchise attachments from %Attachment table
 * @author Till Uhlig
 * @param int \$eid an %Exercise identifier
 * @result 
 * - A, the attachment data
 * - F, the attachment file
 */
?>
 
select 
    A.A_id,
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
    Attachment A
        left join
    File F ON F.F_id = A.F_id
where
    A.E_id = '<?php echo $eid; ?>'