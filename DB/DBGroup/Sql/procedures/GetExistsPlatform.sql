DROP PROCEDURE IF EXISTS `DBGroupGetExistsPlatform`;
CREATE PROCEDURE `DBGroupGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Group';
end;