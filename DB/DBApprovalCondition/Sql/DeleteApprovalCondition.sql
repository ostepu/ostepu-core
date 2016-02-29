<?php
/**
 * @file DeleteApprovalCondition.sql
 * deletes an specified approval condition from %ApprovalCondition table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$apid a %ApprovalCondition identifier
 * @result -
 */
?>

DELETE FROM ApprovalCondition
WHERE
    AC_id = '<?php echo $apid; ?>'