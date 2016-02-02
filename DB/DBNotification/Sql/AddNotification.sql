<?php
/**
 * @file AddNotification.sql
 * inserts an Notification into %Notification table
 * @author  Till Uhlig
 * @result -
 */
?>

INSERT INTO `Notification<?php echo $pre; ?>_<?php echo $courseid; ?>` SET <?php echo $object->getInsertData(); ?>
ON DUPLICATE KEY UPDATE <?php echo $object->getInsertData(); ?>;
select '<?php echo $courseid; ?>' as 'C_id';