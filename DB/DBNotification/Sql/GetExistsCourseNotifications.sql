<?php
/**
 * @file GetExistsCourseNotifications.sql
 * checks whether table exists
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */
?>

show tables like 'Notification<?php echo $pre; ?>_<?php echo $courseid; ?>';