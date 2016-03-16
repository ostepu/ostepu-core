<?php
/**
 * @file DeleteExerciseSheetExerciseFileType.sql
 * deletes specified exercise file types from %ExerciseFileType table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.2.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$esid a %ExerciseSheet identifier
 * @result -
 */
?>

DELETE EFT FROM ExerciseFileType EFT, Exercise E
WHERE
    E.ES_id = '<?php echo $esid; ?>' and E.E_id = EFT.E_id;