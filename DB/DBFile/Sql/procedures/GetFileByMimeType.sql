DROP PROCEDURE IF EXISTS `DBFileGetFileByMimeType`;
CREATE PROCEDURE `DBFileGetFileByMimeType` (IN base varchar(255),IN type varchar(255),IN beginStamp INT,IN endStamp INT)
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
    ((F_mimeType is null and '",base,"' = ':base') or
    (F_mimeType like '",base,"/%' and '",type,"'=':type') or
    (F_mimeType like '",base,"/",base,"')
    )
    and
        ('",beginStamp,"'='0' or F_timeStamp>='",beginStamp,"')
        and
        ('",endStamp,"'='0' or F_timeStamp>='",endStamp,"');");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;