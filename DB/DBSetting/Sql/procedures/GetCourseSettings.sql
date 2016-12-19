<?php
/**
 * @file GetCourseSettings.sql
 * gets all course settings from %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$courseid an %Course identifier
 * @result
 * - S, the Setting data
 */
?>
    
DROP PROCEDURE IF EXISTS `DBSettingGetCourseSettings`;
CREATE PROCEDURE `DBSettingGetCourseSettings` (IN profile varchar(30), IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
select
    S.*,
    concat('",courseid,"','_',S.SET_id) as SET_id
from
    `Setting",profile,"_",courseid,"` S;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;