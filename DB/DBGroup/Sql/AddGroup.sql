<?php
/**
 * @file AddGroup.sql
 * creates a new entry in %Group table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO `Group` SET <?php echo $values; ?>