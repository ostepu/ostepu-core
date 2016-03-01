<?php
/**
 * @file EditExerciseFileType.sql
 * updates an specified exercise file type from %ExerciseFileType table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$eftid a %ExerciseFileType identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseFileType
SET <?php echo $values; ?>
WHERE EFT_id = '<?php echo $eftid; ?>'