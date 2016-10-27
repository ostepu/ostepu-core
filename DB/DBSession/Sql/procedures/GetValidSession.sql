<?php
/**
 * @file GetValidSession.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

DROP PROCEDURE IF EXISTS `DBSessionGetValidSession`;
CREATE PROCEDURE `DBSessionGetValidSession` (IN seid char(32), IN userid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id,
    SE_sessionID
from
    `Session`
where
    U_id = '",userid,"' and SE_sessionID = '",seid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;