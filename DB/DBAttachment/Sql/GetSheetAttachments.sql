<?php
/**
 * @file GetSheetAttachments.sql
 * gets all exerchise sheet attachments from %Attachment table
 * @author Till Uhlig
 * @param int \$esid an %ExerciseSheet identifier
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
    F.F_hash
from
    Exercise E
        join
    (Attachment A
    left join File F ON F.F_id = A.F_id) ON E.E_id = A.E_id
where
    E.ES_id = '<?php echo $esid; ?>'