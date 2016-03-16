<?php
/**
 * @file GetFileByMimeType.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

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