DROP PROCEDURE IF EXISTS `DBUserGetExistsPlatform`;
CREATE PROCEDURE `DBUserGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'User';
end;