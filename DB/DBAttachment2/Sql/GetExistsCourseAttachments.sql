<?php
/**
 * @file GetExistsCourseAttachments.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Attachment<?php echo $pre; ?>_<?php echo $courseid; ?>` A limit 1;