<?php
/**
 * @file DeleteNotification.sql
 * deletes an specified Notification from %Notification table
 * @author  Till Uhlig
 * @param int \$notid a %Notification identifier
 * @result -
 */
?>

DELETE FROM `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
WHERE
    NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'

