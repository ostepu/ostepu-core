<?php
/**
 * @file GetExistsCourseSettings.sql
 * checks whether table exists
 */
?>

show tables like 'Setting<?php echo $pre; ?>_<?php echo $courseid; ?>';