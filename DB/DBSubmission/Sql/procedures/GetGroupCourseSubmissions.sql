<?php
/**
 * @file GetGroupCourseSubmissions.sql
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

DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupCourseSubmissions`;
CREATE PROCEDURE `DBSubmissionGetGroupCourseSubmissions` (IN userid INT,IN courseid INT)
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
    `Group` G
        join
    `Group` G2 ON (G.U_id_leader = '",userid,"'
        and G.U_id_member = G2.U_id_member
        and G.C_id = '",courseid,"'
        and G2.ES_id = G.ES_id)
        join
    (Submission S
    join Exercise E ON (S.E_id = E.E_id and E.C_id = '",courseid,"')) ON (G2.ES_id = E.ES_id and G2.U_id_leader = S.U_id)
        left join
    File F ON (S.F_id_file = F.F_id)
        left join
    SelectedSubmission SS ON (S.S_id = SS.S_id_selected
        and S.E_id = SS.E_id);");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;
