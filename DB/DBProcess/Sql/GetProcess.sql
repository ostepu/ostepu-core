<?php
/**
 * @file GetProcess.sql
 * gets a process from %Process table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$processid an %Process identifier
 * @result
 * - PRO, the process data
 * - CO, the component data
 */
?>

SET @course = '<?php echo Process::getCourseFromProcessId($processid); ?>';
SET @statement =
concat(
"select
    concat('", @course ,"','_',PRO.PRO_id) as PRO_id,
    PRO.E_id,
    PRO.E_id as E_id2,
    PRO.PRO_parameter,
    PRO.CO_id_target,
    E.ES_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link,
    E.E_linkName,
    CO.CO_id,
    CO.CO_name,
    CO.CO_address,
    concat('", @course ,"','_',A.A_id) as A_id_PRO1,
    A.E_id as E_id_PRO1,
    F.F_id as F_id_PRO1,
    F.F_displayName as F_displayName_PRO1,
    F.F_address as F_address_PRO1,
    F.F_timeStamp as F_timeStamp_PRO1,
    F.F_fileSize as F_fileSize_PRO1,
    F.F_comment as F_comment_PRO1,
    F.F_hash as F_hash_PRO1,
    F.F_mimeType as F_mimeType_PRO1,
    concat('", @course ,"','_',A2.A_id) as A_id_PRO2,
    A2.E_id as E_id_PRO2,
    F2.F_id as F_id_PRO2,
    F2.F_displayName as F_displayName_PRO2,
    F2.F_address as F_address_PRO2,
    F2.F_timeStamp as F_timeStamp_PRO2,
    F2.F_fileSize as F_fileSize_PRO2,
    F2.F_comment as F_comment_PRO2,
    F2.F_hash as F_hash_PRO2,
    F2.F_mimeType as F_mimeType_PRO2
from
    `Process<?php echo $pre; ?>_", @course, "` PRO
        left join
    `Component` CO ON (PRO.CO_id_target = CO.CO_id)
            left join
    `Exercise` E ON (E.E_id = PRO.E_id)
                left join
    `Attachment_processAttachment_", @course, "` A ON (PRO.PRO_id = A.PRO_id)
                left join
    `File` F ON F.F_id = A.F_id
                left join
    `Attachment_processWorkFiles_", @course, "` A2 ON (PRO.PRO_id = A2.PRO_id)
                left join
    `File` F2 ON F2.F_id = A2.F_id
where
    PRO.PRO_id = '<?php echo Process::getIdFromProcessId($processid); ?>'");

PREPARE stmt1 FROM @statement;
EXECUTE stmt1;