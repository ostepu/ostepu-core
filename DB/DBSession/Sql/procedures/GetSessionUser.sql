DROP PROCEDURE IF EXISTS `DBSessionGetSessionUser`;
CREATE PROCEDURE `DBSessionGetSessionUser` (IN seid char(32))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id,
    SE_sessionID
from
    `Session`
where
    SE_sessionID = '",seid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;