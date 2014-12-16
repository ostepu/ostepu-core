DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetSheetSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetSheetSelected` (IN esid varchar(120))
begin
select 
    U_id_leader, S_id_selected, E_id, ES_id
from
    SelectedSubmission
where
    ES_id = esid;
end;