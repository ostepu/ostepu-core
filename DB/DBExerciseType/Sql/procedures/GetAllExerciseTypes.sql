DROP PROCEDURE IF EXISTS `DBExerciseTypeGetAllExerciseTypes`;
CREATE PROCEDURE `DBExerciseTypeGetAllExerciseTypes` ()
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    ET_id, ET_name
from
    ExerciseType;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;