<?php
/**
 * @file GetExistsCourseNotifications.sql
 * checks whether table exists
 */
?>

show tables like 'Notification<?php echo $pre; ?>_<?php echo $courseid; ?>';