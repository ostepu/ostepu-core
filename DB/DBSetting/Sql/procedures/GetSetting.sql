<?php
/**
 * @file GetSetting.sql
 * gets a setting from %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$setid an %Setting identifier
 * @result
 * - S, the Setting data
 */
?>

DROP PROCEDURE IF EXISTS `DBSettingGetSetting`;
CREATE PROCEDURE `DBSettingGetSetting` (IN profile varchar(30), IN courseid INT, IN setid INT)
READS SQL DATA
begin
SET @s = concat("
select
    S.*,
    concat('",courseid,"','_',S.SET_id) as SET_id
from
    `Setting",profile,"_",courseid,"` S
WHERE SET_id = '",setid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;
