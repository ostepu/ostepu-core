/**
 * @file EditAttachment.sql
 * updates an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result -
 */

UPDATE `Attachment{pre}_".Attachment::getCourseFromAttachmentId($aid)."`
SET {$object->getInsertData()}
WHERE A_id = '".Attachment::getIdFromAttachmentId($aid)."'