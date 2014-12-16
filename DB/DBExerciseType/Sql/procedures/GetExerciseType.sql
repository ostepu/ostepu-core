DROP PROCEDURE IF EXISTS `DBExerciseTypeGetExerciseType`;
CREATE PROCEDURE `DBExerciseTypeGetExerciseType` (IN etid varchar(120))
begin
select 
    ET_id, ET_name
from
    ExerciseType
where
    ET_id = etid;
end;