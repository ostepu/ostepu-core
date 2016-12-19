<?php
/**
 * @file GetRedirect.sql
 * gets a redirect from %Redirect table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @param int \$redid an %Redirect identifier
 * @result
 * - S, the Redirect data
 */
?>

DROP PROCEDURE IF EXISTS `DBRedirectGetRedirect`;
CREATE PROCEDURE `DBRedirectGetRedirect` (IN profile varchar(30), IN courseid INT, IN redid INT)
READS SQL DATA
begin
SET @s = concat("
select
    S.*,
    concat('",courseid,"','_',S.RED_id) as RED_id
from
    `Redirect",profile,"_",courseid,"` S
WHERE RED_id = '",redid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;

