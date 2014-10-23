<?php
/**
 * @file GetExistsCourseChoices.sql
 * checks whether table exists
 */
?>

show tables like 'Choice<?php echo $preChoice; ?>_<?php echo $courseid; ?>';