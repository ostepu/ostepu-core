<?php
/** 
 * @file DeleteAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result -
 */
?>
 
DELETE FROM `Attachment<?php echo $pre; ?>_<?php echo Attachment::getCourseFromAttachmentId($aid); ?>`
WHERE
    A_id = '<?php echo Attachment::getIdFromAttachmentId($aid); ?>'

