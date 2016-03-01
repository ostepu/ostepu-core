<?php
/**
 * @file GetExercise.sql
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

DROP PROCEDURE IF EXISTS `DBExerciseGetExercise`;
CREATE PROCEDURE `DBExerciseGetExercise` (IN eid INT,IN sub varchar(12))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    E.E_id,
    E.ES_id,
    E.C_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link,
    E.E_linkName,
    E.E_submittable,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    EFT_id,
    EFT_text,
    S.U_id as U_id2,
    S.S_id as S_id2,
    S.F_id_file as F_id_file2,
    S.S_comment as S_comment2,
    S.S_date as S_date2,
    SS.S_id_selected as S_selected2,
    S.S_accepted as S_accepted2,
    S.S_leaderId as S_leaderId2,
    S.S_hideFile as S_hideFile2,
    S.E_id as E_id2
from
    Exercise E
        left join
    ExerciseFileType EFT ON E.E_id = EFT.E_id
        left join
    Attachment A ON E.E_id = A.E_id
        left join
    File F on A.F_id = F.F_id
        left join
    Submission S ON ('",sub,"'<>'nosubmission' and S.E_id = E.E_id)
        left join
    SelectedSubmission SS ON S.S_id = SS.S_id_selected
where
    E.E_id = '",eid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;