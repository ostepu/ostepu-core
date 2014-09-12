<?php
/**
 * @file GetExistsCourseChoices.sql
 * checks whether table exists
 */
?>

select 
    count(1)
from
    `Choice<?php echo $preChoice; ?>_<?php echo $courseid; ?>` FO limit 1;