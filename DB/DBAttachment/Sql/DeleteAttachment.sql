<?php
/**
 * @file DeleteAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$aid a %Attachment identifier
 * @result -
 */
?>

DELETE FROM Attachment
WHERE
    A_id = '<?php echo $aid; ?>'

