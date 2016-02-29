<?php
/**
 * @file DeleteNotification.sql
 * deletes an specified Notification from %Notification table
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$notid a %Notification identifier
 * @result -
 */
?>

DELETE FROM `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
WHERE
    NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'

