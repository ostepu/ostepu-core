<?php
/**
 * @file GetAllExerciseTypes.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseTypeGetAllExerciseTypes`;
CREATE PROCEDURE `DBExerciseTypeGetAllExerciseTypes` ()
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    ET_id, ET_name
from
    ExerciseType;");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;