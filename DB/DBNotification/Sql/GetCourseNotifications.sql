<?php
/**
 * @file GetCourseNotifications.sql
 * gets all course settings from %Notification table
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$courseid an %Course identifier
 * @result
 * - S, the Notification data
 */
?>

select
    N.*,
    concat('<?php echo $courseid; ?>','_',N.NOT_id) as NOT_id
from
    `Notification<?php echo $pre; ?>_<?php echo $courseid; ?>` N