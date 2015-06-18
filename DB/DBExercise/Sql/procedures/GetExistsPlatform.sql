DROP PROCEDURE IF EXISTS `DBExerciseGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Exercise';
end;