<?php
/**
 * @file GetExistsCourseSettings.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

/**
 * @file GetExistsCourseSettings.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

DROP PROCEDURE IF EXISTS `DBSettingGetExistsCourseSettings`;
CREATE PROCEDURE `DBSettingGetExistsCourseSettings` (IN profile varchar(30), IN courseid INT)
READS SQL DATA
begin
SET @s = concat("show tables like 'Setting",profile,"_",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;