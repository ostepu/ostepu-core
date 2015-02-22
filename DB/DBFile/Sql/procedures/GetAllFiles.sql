DROP PROCEDURE IF EXISTS `DBFileGetAllFiles`;
CREATE PROCEDURE `DBFileGetAllFiles` (IN beginStamp INT,IN endStamp INT)
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
        ('",beginStamp,"'=':beginStamp' or F_timeStamp>='",beginStamp,"')
        and 
        ('",endStamp,"'=':endStamp' or F_timeStamp>='",endStamp,"');");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;