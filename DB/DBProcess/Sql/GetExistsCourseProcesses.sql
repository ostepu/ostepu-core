<?php
/**
 * @file GetExistsCourseProcess.sql
 * checks whether table exists
 */
?>

show tables like 'Process<?php echo $pre; ?>_<?php echo $courseid; ?>';