<?php
/**
 * @file GetSessionUser.sql
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

DROP PROCEDURE IF EXISTS `DBSessionGetSessionUser`;
CREATE PROCEDURE `DBSessionGetSessionUser` (IN profile varchar(30), IN seid char(32))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id,
    SE_sessionID
from
    `Session",profile,"`
where
    SE_sessionID = '",seid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;