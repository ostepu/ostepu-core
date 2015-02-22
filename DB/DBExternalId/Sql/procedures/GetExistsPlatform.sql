DROP PROCEDURE IF EXISTS `DBExternalIdGetExistsPlatform`;
CREATE PROCEDURE `DBExternalIdGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExternalId';
end;