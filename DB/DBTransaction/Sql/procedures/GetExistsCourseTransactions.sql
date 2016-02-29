<?php
/**
 * @file GetExistsCourseTransactions.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */

/**
 * @file GetExistsCourseTransactions.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>';