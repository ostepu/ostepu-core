<?php
/**
 * @file GetCourseAttachments.sql
 * gets all course attachments from %Attachment table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$courseid an %Course identifier
 * @result
 * - A, the attachment data
 * - F, the attachment file
 */
?>

select
    concat('<?php echo $courseid; ?>','_',A.A_id) as A_id,
    concat('<?php echo $courseid; ?>','_',A.PRO_id) as PRO_id,
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
    `Attachment<?php echo $pre; ?>_<?php echo $courseid; ?>` A
    left join `File` F ON F.F_id = A.F_id