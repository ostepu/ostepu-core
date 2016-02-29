<?php
/**
 * @file GetSessionUser.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

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