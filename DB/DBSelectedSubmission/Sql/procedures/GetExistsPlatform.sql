DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetExistsPlatform`;
CREATE PROCEDURE `DBSelectedSubmissionGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'SelectedSubmission';
end;