<?php
/**
 * @file EditExerciseSheet.sql
 * updates an specified exercise sheet from %ExerciseSheet table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$esid an %ExerciseSheet identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseSheet
SET <?php echo $values; ?>
WHERE ES_id = '<?php echo $esid; ?>'