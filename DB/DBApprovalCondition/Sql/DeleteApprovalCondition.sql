<?php
/** 
 * @file DeleteApprovalCondition.sql
 * deletes an specified approval condition from %ApprovalCondition table
 * @author  Till Uhlig
 * @param int \$apid a %ApprovalCondition identifier
 * @result -
 */
?>

DELETE FROM ApprovalCondition 
WHERE
    AC_id = '<?php echo $apid; ?>'