DROP PROCEDURE IF EXISTS `DBSessionGetAllSessions`;
CREATE PROCEDURE `DBSessionGetAllSessions` ()
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id,
    SE_sessionID
from
    `Session`;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;