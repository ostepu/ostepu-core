<?php
/**
 * @file EditExerciseType.sql
 * updates an specified possible type from %ExerciseType table
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
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseType
SET <?php echo $values; ?>
WHERE ET_id = '<?php echo $etid; ?>'