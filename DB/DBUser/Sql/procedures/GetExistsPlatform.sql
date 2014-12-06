DROP PROCEDURE IF EXISTS `DBUserGetExistsPlatform`;
CREATE PROCEDURE `DBUserGetExistsPlatform` ()
begin
show tables like 'User';
end;