<?php
/**
 * @file GetApprovalCondition.sql
 * gets an specified approval condition from %ApprovalCondition table
 * @author  Till Uhlig
 * @param int \$apid a %ApprovalCondition identifier
 * @result AC_id, C_id, ET_id, AC_percentage
 */
?>

select 
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition
where
    AC_id = '<?php echo $apid; ?>'