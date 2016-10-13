<?php
/**
 * @file GetCourseRedirects.sql
 * gets all course redirect from %Redirect table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @param int \$courseid an %Course identifier
 * @result
 * - S, the Redirect data
 */
?>

DROP PROCEDURE IF EXISTS `DBRedirectGetCourseRedirects`;
CREATE PROCEDURE `DBRedirectGetCourseRedirects` (IN profile varchar(30), IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
select
    S.*,
    concat('",courseid,"','_',S.RED_id) as RED_id
from
    `Redirect",profile,"_",courseid,"` S;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;

