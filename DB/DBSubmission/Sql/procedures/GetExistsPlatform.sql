DROP PROCEDURE IF EXISTS `DBSubmissionGetExistsPlatform`;
CREATE PROCEDURE `DBSubmissionGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Submission';
end;