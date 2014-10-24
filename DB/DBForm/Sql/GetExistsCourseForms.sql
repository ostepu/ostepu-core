<?php
/**
 * @file GetExistsCourseForms.sql
 * checks whether table exists
 */
?>

show tables like 'Form_<?php echo $courseid; ?>';