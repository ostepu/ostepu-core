<?php
/** 
 * @file DeleteExerciseFileAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @param int \$fileid a %File identifier
 * @result -
 */
?>
 
DELETE FROM Attachment 
WHERE
    E_id = '<?php echo $eid; ?>' and F_id = '<?php echo $fileid; ?>'

