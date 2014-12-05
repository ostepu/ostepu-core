DROP PROCEDURE IF EXISTS `DBExerciseTypeGetAllExerciseTypes`;
CREATE PROCEDURE `DBExerciseTypeGetAllExerciseTypes` ()
begin
select 
    ET_id, ET_name
from
    ExerciseType;
end;