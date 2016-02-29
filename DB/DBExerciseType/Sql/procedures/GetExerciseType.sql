<?php
/**
 * @file GetExerciseType.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseTypeGetExerciseType`;
CREATE PROCEDURE `DBExerciseTypeGetExerciseType` (IN etid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    ET_id, ET_name
from
    ExerciseType
where
    ET_id = '",etid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;