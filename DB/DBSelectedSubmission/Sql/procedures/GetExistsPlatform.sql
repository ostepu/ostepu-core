DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetExistsPlatform`;
CREATE PROCEDURE `DBSelectedSubmissionGetExistsPlatform` ()
begin
show tables like 'SelectedSubmission';
end;