<?php
/**
 * @file GetExistsCourseNotifications.sql
 * checks whether table exists
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

show tables like 'Notification<?php echo $pre; ?>_<?php echo $courseid; ?>';