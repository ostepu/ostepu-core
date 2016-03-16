<?php
/**
 * @file DeleteExerciseFileType.sql
 * deletes a specified exercise file type from %ExerciseFileType table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$eftid a %ExerciseFileType identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType
WHERE
    EFT_id = '<?php echo $eftid; ?>'