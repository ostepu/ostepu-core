<?php
/**
 * @file GetExistsPlatform.sql
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

DROP PROCEDURE IF EXISTS `DBCourseGetExistsPlatform`;
CREATE PROCEDURE `DBCourseGetExistsPlatform` (IN profile varchar(30))
READS SQL DATA
begin
SET @s = concat("show tables like 'Course",profile,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;