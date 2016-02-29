<?php
/**
 * @file AddApprovalCondition.sql
 * inserts an approval condition into %ApprovalCondition table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO ApprovalCondition SET <?php echo $values; ?>