DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExerciseSheetURL`;
CREATE PROCEDURE `DBExerciseSheetGetExerciseSheetURL` (IN esid INT)
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
    F.F_mimeType
from
    ExerciseSheet ES
        left join
    File F ON F.F_id = ES.F_id_file
where
    ES.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;