DROP PROCEDURE IF EXISTS `DBSubmissionGetAllSubmissions`;
CREATE PROCEDURE `DBSubmissionGetAllSubmissions` (IN selected varchar(8),IN beginStamp INT,IN endStamp INT)
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
    Submission S
        left join
    File F on (S.F_id_file = F.F_id)
        left join
    SelectedSubmission SS on (S.S_id = SS.S_id_selected)
where ('",selected,"'<>'selected' or  S.S_id = SS.S_id_selected)
and ('",beginStamp,"'='0' or S.S_date>='",beginStamp,"')
and ('",endStamp,"'='0' or S.S_date<='",endStamp,"');");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;