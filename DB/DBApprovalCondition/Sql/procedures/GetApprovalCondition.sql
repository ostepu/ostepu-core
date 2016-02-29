<?php
/**
 * @file GetApprovalCondition.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBApprovalConditionGetApprovalCondition`;
CREATE PROCEDURE `DBApprovalConditionGetApprovalCondition` (IN apid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition
where
    AC_id = '",apid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;