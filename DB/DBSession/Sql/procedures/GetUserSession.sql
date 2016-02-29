<?php
/**
 * @file GetUserSession.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBSessionGetUserSession`;
CREATE PROCEDURE `DBSessionGetUserSession` (IN userid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id,
    SE_sessionID
from
    `Session`
where
    U_id = '",userid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;