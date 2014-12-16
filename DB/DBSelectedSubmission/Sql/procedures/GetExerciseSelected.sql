DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetExerciseSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetExerciseSelected` (IN eid varchar(120))
begin
select 
    U_id_leader, S_id_selected, E_id, ES_id
from
    SelectedSubmission
where
    E_id = eid;
end;