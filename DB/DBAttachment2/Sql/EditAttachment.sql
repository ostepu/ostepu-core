<?php
/**
 * @file EditAttachment.sql
 * updates an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result -
 */
?>

UPDATE `Attachment<?php echo $pre; ?>_<?php echo Attachment::getCourseFromAttachmentId($aid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE A_id = '<?php echo Attachment::getIdFromAttachmentId($aid); ?>'