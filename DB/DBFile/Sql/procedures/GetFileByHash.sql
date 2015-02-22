DROP PROCEDURE IF EXISTS `DBFileGetFileByHash`;
CREATE PROCEDURE `DBFileGetFileByHash` (IN hash varchar(40))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE 
    F_id,
    F_displayName,
    F_address,
    F_timeStamp,
    F_fileSize,
    F_comment,
    F_hash,
    F_mimeType
from
    File
where
    F_hash = '",hash,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;