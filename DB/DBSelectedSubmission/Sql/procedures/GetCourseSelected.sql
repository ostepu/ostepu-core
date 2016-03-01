<?php
/**
 * @file GetCourseSelected.sql
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

DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetCourseSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetCourseSelected` (IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    SS.U_id_leader, SS.S_id_selected, SS.E_id, SS.ES_id
from
    SelectedSubmission SS
    join
    ExerciseSheet ES ON ES.ES_id = SS.ES_id
where
    ES.C_id = '",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;