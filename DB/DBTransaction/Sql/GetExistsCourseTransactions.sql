<?php
/**
 * @file GetExistsCourseTransactions.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>` T limit 1;