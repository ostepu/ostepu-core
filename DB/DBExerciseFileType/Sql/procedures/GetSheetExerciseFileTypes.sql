<?php
/**
 * @file GetSheetExerciseFileTypes.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetSheetExerciseFileTypes`;
CREATE PROCEDURE `DBExerciseFileTypeGetSheetExerciseFileTypes` (IN esid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    EX.EFT_id, EX.EFT_text, EX.E_id
from
    ExerciseFileType EX
    join Exercise E on (EX.E_id = E.E_id)
where
    E.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;