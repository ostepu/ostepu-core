<?php
/**
 * @file GetSheetExercises.sql
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

DROP PROCEDURE IF EXISTS `DBExerciseSheetGetSheetExercises`;
CREATE PROCEDURE `DBExerciseSheetGetSheetExercises` (IN esid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    E.E_id,
    E.ES_id,
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
    F.F_hash,
    F.F_comment,
    F.F_mimeType,
    EFT_id,
    EFT_text
from
    Exercise E
            left join
    ExerciseFileType EFT ON E.E_id = EFT.E_id
        left join
    Attachment A ON E.E_id = A.E_id
        left join
    File F on A.F_id = F.F_id
where
    E.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;