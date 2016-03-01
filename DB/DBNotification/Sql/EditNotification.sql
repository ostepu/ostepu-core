<?php
/**
 * @file EditNotification.sql
 * updates an specified setting from %Notification table
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.4.3
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @result -
 */
?>

UPDATE `Notification<?php echo $pre; ?>_<?php echo Notification::getCourseFromNotificationId($notid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE NOT_id = '<?php echo Notification::getIdFromNotificationId($notid); ?>'