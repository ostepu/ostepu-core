<?php
/**
 * @file EditExerciseFileType.sql
 * updates an specified exercise file type from %ExerciseFileType table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$eftid a %ExerciseFileType identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ExerciseFileType
SET <?php echo $values; ?>
WHERE EFT_id = '<?php echo $eftid; ?>'