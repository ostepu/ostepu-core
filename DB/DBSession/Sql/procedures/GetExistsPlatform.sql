DROP PROCEDURE IF EXISTS `DBSessionGetExistsPlatform`;
CREATE PROCEDURE `DBSessionGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Session';
end;