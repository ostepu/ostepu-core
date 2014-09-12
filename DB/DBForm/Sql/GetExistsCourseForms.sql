<?php
/**
 * @file GetExistsCourseForms.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Form_<?php echo $courseid; ?>` FO limit 1;