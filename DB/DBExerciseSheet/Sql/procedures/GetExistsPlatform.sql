<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseSheetGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExerciseSheet';
end;