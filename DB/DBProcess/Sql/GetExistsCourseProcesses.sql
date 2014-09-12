<?php
/**
 * @file GetExistsCourseProcess.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Process<?php echo $pre; ?>_<?php echo $courseid; ?>` PRO limit 1;