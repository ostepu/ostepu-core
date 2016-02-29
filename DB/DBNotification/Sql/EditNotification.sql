<?php
/**
 * @file EditNotification.sql
 * updates an specified setting from %Notification table
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @result -
 */
?>

UPDATE `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'