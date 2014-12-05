DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetCourseSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetCourseSelected` (IN courseid varchar(120))
begin
select 
    SS.U_id_leader, SS.S_id_selected, SS.E_id, SS.ES_id
from
    SelectedSubmission SS
    join 
    ExerciseSheet ES ON ES.ES_id = SS.ES_id
where
    ES.C_id = courseid;
end;