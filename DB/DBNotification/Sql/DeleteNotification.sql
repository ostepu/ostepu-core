<?php
/**
 * @file DeleteNotification.sql
 * deletes an specified Notification from %Notification table
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$notid a %Notification identifier
 * @result -
 */
?>

DELETE FROM `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
WHERE
    NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'

