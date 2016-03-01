<?php
/**
 * @file DeleteExerciseExerciseFileType.sql
 * deletes specified exercise file types from %ExerciseFileType table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.2.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM ExerciseFileType
WHERE
    E_id = '<?php echo $eid; ?>';