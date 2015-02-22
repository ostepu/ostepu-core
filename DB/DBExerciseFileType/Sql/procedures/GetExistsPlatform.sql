DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseFileTypeGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExerciseFileType';
end;