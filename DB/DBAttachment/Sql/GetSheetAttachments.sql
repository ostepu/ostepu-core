<?php
/**
 * @file GetSheetAttachments.sql
 * gets all exerchise sheet attachments from %Attachment table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
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
    F.F_hash,
    F.F_mimeType
from
    Exercise E
        join
    (Attachment A
    left join File F ON F.F_id = A.F_id) ON E.E_id = A.E_id
where
    E.ES_id = '<?php echo $esid; ?>'