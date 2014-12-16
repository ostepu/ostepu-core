DROP PROCEDURE IF EXISTS `DBExerciseTypeGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseTypeGetExistsPlatform` ()
begin
show tables like 'ExerciseType';
end;