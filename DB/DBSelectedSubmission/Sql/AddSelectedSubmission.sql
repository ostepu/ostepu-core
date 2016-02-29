<?php
/**
 * @file AddSelectedSubmission.sql
 * inserts a selected submission row into %SelectedSubmission table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO `SelectedSubmission` SET <?php echo $values; ?>