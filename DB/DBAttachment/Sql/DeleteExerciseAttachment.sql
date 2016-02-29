<?php
/**
 * @file DeleteExerciseAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM Attachment
WHERE
    E_id = '<?php echo $eid; ?>'

