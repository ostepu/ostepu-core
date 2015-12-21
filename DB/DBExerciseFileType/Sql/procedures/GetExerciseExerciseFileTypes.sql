DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExerciseExerciseFileTypes`;
CREATE PROCEDURE `DBExerciseFileTypeGetExerciseExerciseFileTypes` (IN eid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    EFT_id, EFT_text, E_id
from
    ExerciseFileType
where
    E_id = '",eid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;