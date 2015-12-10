DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetCourseSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetCourseSelected` (IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    SS.U_id_leader, SS.S_id_selected, SS.E_id, SS.ES_id
from
    SelectedSubmission SS
    join
    ExerciseSheet ES ON ES.ES_id = SS.ES_id
where
    ES.C_id = '",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;