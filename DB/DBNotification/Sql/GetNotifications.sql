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
    N.*
from
    `Notification<?php echo $pre; ?>` N
where
    N.NOT_begin >= UNIX_TIMESTAMP() and N.NOT_end <= UNIX_TIMESTAMP();