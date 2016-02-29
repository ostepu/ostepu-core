<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'Exercise';
end;