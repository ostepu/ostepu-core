DROP PROCEDURE IF EXISTS `DBInvitationGetExistsPlatform`;
CREATE PROCEDURE `DBInvitationGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Invitation';
end;