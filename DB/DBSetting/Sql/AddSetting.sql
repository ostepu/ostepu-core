<?php
/**
 * @file AddSetting.sql
 * inserts an Setting into %Setting table
 * @author  Till Uhlig
 * @result -
 */
?>

INSERT INTO `Setting<?php echo $pre; ?>_<?php echo $courseid; ?>` SET <?php echo $object->getInsertData(); ?>
ON DUPLICATE KEY UPDATE <?php echo $object->getInsertData(); ?>;
select '<?php echo $courseid; ?>' as 'C_id';