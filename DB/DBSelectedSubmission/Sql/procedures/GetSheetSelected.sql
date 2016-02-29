<?php
/**
 * @file GetSheetSelected.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBSelectedSubmissionGetSheetSelected`;
CREATE PROCEDURE `DBSelectedSubmissionGetSheetSelected` (IN esid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    U_id_leader, S_id_selected, E_id, ES_id
from
    SelectedSubmission
where
    ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;