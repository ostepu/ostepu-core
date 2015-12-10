<?php
/**
 * @file DeleteExerciseAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author  Till Uhlig
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM Attachment
WHERE
    E_id = '<?php echo $eid; ?>'

