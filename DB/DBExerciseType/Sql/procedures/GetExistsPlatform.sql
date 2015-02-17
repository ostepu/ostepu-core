DROP PROCEDURE IF EXISTS `DBExerciseTypeGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseTypeGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExerciseType';
end;