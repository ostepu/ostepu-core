DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseSheetGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExerciseSheet';
end;