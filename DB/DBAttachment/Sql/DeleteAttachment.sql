<?php
/** 
 * @file DeleteAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$aid a %Attachment identifier
 * @result -
 */
?>
 
DELETE FROM Attachment 
WHERE
    A_id = '<?php echo $aid; ?>'

