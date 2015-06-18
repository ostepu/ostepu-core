DROP PROCEDURE IF EXISTS `DBFileGetExistsPlatform`;
CREATE PROCEDURE `DBFileGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'File';
end;