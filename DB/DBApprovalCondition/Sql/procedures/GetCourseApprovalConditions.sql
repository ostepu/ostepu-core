<?php
/**
 * @file GetCourseApprovalConditions.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBApprovalConditionGetCourseApprovalConditions`;
CREATE PROCEDURE `DBApprovalConditionGetCourseApprovalConditions` (IN courseid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition
where
    C_id = '",courseid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;