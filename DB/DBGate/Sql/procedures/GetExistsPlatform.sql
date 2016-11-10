<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.6.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */
?>

DROP PROCEDURE IF EXISTS `DBGateGetExistsPlatform`;
CREATE PROCEDURE `DBGateGetExistsPlatform` ()
READS SQL DATA
begin
SET @s = concat("show tables like 'GateProfile",profile,"';show tables like 'GateRule",profile,"';show tables like 'GateAuth",profile,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;