DROP PROCEDURE IF EXISTS `DBApprovalConditionGetExistsPlatform`;
CREATE PROCEDURE `DBApprovalConditionGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ApprovalCondition';
end;