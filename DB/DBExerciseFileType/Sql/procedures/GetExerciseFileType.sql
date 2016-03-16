<?php
/**
 * @file GetExerciseFileType.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

DROP PROCEDURE IF EXISTS `DBExerciseFileTypeGetExerciseFileType`;
CREATE PROCEDURE `DBExerciseFileTypeGetExerciseFileType` (IN eftid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    EFT_id, EFT_text, E_id
from
    ExerciseFileType
where
    EFT_id = '",eftid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;