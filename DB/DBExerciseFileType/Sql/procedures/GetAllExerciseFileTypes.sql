DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetAllExerciseFileTypes`;
CREATE PROCEDURE `DBExerciseFileTypeGetAllExerciseFileTypes` ()
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    EFT_id, EFT_text, E_id
from
    ExerciseFileType;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;