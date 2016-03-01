DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExerciseFileType`;
CREATE PROCEDURE `DBExerciseFileTypeGetExerciseFileType` (IN eftid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    EFT_id, EFT_text, E_id
from
    ExerciseFileType
where
    EFT_id = '",eftid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;