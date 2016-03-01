<?php
/**
 * @file GetExerciseSheetURL.sql
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

DROP PROCEDURE IF EXISTS `DBExerciseSheetGetExerciseSheetURL`;
CREATE PROCEDURE `DBExerciseSheetGetExerciseSheetURL` (IN esid INT)
READS SQL DATA
begin
SET @s = concat("
select SQL_CACHE
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    F.F_mimeType
from
    ExerciseSheet ES
        left join
    File F ON F.F_id = ES.F_id_file
where
    ES.ES_id = '",esid,"';");
PREPARE stmt1 FROM @s;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;
end;