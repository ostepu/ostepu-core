<?php
/**
 * @file GetExistsPlatform.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExistsPlatform`;
CREATE PROCEDURE `DBExerciseFileTypeGetExistsPlatform` ()
READS SQL DATA
begin
show tables like 'ExerciseFileType';
end;