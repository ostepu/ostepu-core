<?php
/**
 * @file GetAllSubmissions.sql
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

DROP PROCEDURE IF EXISTS `DBSubmissionGetAllSubmissions`;
CREATE PROCEDURE `DBSubmissionGetAllSubmissions` (IN profile varchar(30), IN selectedSubmissionProfile varchar(30), IN fileProfile varchar(30), IN selected varchar(8),IN beginStamp INT,IN endStamp INT)
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
    SS.S_id_selected,
    S.S_accepted,
    S.S_flag,
    S.S_leaderId,
    S.S_hideFile,
    S.E_id,
    S.ES_id
from
    `Submission",profile,"` S
        left join
    `File",fileProfile,"` F on (S.F_id_file = F.F_id)
        left join
    `SelectedSubmission",selectedSubmissionProfile,"` SS on (S.S_id = SS.S_id_selected)
where ('",selected,"'<>'selected' or  S.S_id = SS.S_id_selected)
and ('",beginStamp,"'='0' or S.S_date>='",beginStamp,"')
and ('",endStamp,"'='0' or S.S_date<='",endStamp,"');");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;