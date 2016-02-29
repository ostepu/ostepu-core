<?php
/**
 * @file GetCourseNotifications.sql
 * gets all course settings from %Notification table
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$courseid an %Course identifier
 * @result
 * - S, the Notification data
 */
?>

select
    concat('<?php echo $courseid; ?>','_',N.NOT_id) as NOT_id,
    N.*
from
    `Notification<?php echo $pre; ?>_<?php echo $courseid; ?>` N
where
    N.NOT_begin >= UNIX_TIMESTAMP() and N.NOT_end <= UNIX_TIMESTAMP();