DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExerciseSheet`;
CREATE PROCEDURE `DBExerciseSheetGetExerciseSheet` (IN esid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    ES.ES_id,
    ES.C_id,
    ES.ES_endDate,
    ES.ES_startDate,
    ES.ES_groupSize,
    ES.ES_name,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    F.F_mimeType,
    F2.F_id as F_id2,
    F2.F_displayName as F_displayName2,
    F2.F_address as F_address2,
    F2.F_timeStamp as F_timeStamp2,
    F2.F_fileSize as F_fileSize2,
    F2.F_comment as F_comment2,
    F2.F_hash as F_hash2,
    F2.F_mimeType as F_mimeType2
from
    (ExerciseSheet ES
    left join File F ON F.F_id = ES.F_id_file)
        left join
    File F2 ON (F2.F_id = ES.F_id_sampleSolution)
where
    ES.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;