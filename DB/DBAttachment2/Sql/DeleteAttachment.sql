/** 
 * @file DeleteAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result -
 */
 
DELETE FROM `Attachment{pre}_".Attachment::getCourseFromAttachmentId($aid)."`
WHERE
    A_id = '".Attachment::getIdFromAttachmentId($aid)."'

