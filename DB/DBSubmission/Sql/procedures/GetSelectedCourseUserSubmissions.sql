<?php
/**
 * @file GetSelectedCourseUserSubmissions.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBSubmissionGetSelectedCourseUserSubmissions`;
CREATE PROCEDURE `DBSubmissionGetSelectedCourseUserSubmissions` (IN profile varchar(30), IN selectedSubmissionProfile varchar(30), IN fileProfile varchar(30), IN exerciseProfile varchar(30), IN courseid INT,IN userid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    F.F_mimeType,
    S.U_id,
    S.S_id,
    S.F_id_file,
    S.S_comment,
    S.S_date,
    SS.S_id_selected as S_selected,
    S.S_accepted,
    S.S_flag,
    S.S_leaderId,
    S.S_hideFile,
    S.E_id,
    S.ES_id
from
    `Submission",profile,"` S
    join `Exercise",exerciseProfile,"` E ON (S.E_id = E.E_id and E.C_id = '",courseid,"' and S.U_id = '",userid,"')
        left join
    `File",fileProfile,"` F ON (S.F_id_file = F.F_id)
        join
    `SelectedSubmission",selectedSubmissionProfile,"` SS ON (S.S_id = SS.S_id_selected
        and S.E_id = SS.E_id);");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;
