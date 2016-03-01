<?php
/**
 * @file EditNotification.sql
 * updates an specified setting from %Notification table
 * @author  Till Uhlig
 * @result -
 */
?>

UPDATE `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'