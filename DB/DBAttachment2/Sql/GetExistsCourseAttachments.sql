<?php
/**
 * @file GetExistsCourseAttachments.sql
 * checks whether table exists
 */
?>

show tables like 'Attachment<?php echo $pre; ?>_<?php echo $courseid; ?>';