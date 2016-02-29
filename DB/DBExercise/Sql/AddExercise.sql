<?php
/**
 * @file AddExercise.sql
 * inserts an exercise into %Exercise table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO Exercise SET <?php echo $values; ?>