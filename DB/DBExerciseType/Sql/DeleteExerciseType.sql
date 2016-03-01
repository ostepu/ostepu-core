<?php
/**
 * @file DeleteExerciseType.sql
 * deletes a specified possible type from %ExerciseType table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @author Felix Schmidt <Fiduz@Live.de>
 * @date 2014
 *
 * @param int \$etid a %ExerciseType identifier
 * @result -
 */
?>

DELETE FROM ExerciseType
WHERE
    ET_id = '<?php echo $etid; ?>'