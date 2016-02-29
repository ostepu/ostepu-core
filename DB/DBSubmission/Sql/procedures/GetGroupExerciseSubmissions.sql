<?php
/**
 * @file GetGroupExerciseSubmissions.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBSubmissionGetGroupExerciseSubmissions`;
CREATE PROCEDURE `DBSubmissionGetGroupExerciseSubmissions` (IN userid INT,IN eid INT)
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
   (Submission S
    left join File F ON (S.F_id_file = F.F_id
        and S.E_id = '",eid,"')
    left join SelectedSubmission SS ON (S.S_id = SS.S_id_selected
        and S.E_id = SS.E_id))
        join


(`Group` G
        join
    `Group` G2 ON (G.U_id_leader = '",userid,"'
        and G.U_id_member = G2.U_id_member
        and G2.ES_id = G.ES_id)
)
         ON (G.ES_id = S.ES_id and S.U_id = G2.U_id_leader);");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;