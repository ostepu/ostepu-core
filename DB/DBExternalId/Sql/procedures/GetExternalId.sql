<?php
/**
 * @file GetExternalId.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExternalIdGetExternalId`;
CREATE PROCEDURE `DBExternalIdGetExternalId` (IN exid VARCHAR(255))
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize,
    EX.EX_id
from
    ExternalId EX left join Course C ON (EX.C_id = C.C_id)
where
    EX.EX_id = '",exid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;