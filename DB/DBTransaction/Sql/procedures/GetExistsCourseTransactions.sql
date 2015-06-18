<?php
/**
 * @file GetExistsCourseTransactions.sql
 * checks whether table exists
 */
?>

show tables like 'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>';