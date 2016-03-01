<?php
/**
 * @file DeleteExerciseAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.2.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$eid a %Exercise identifier
 * @result -
 */
?>

DELETE FROM Attachment
WHERE
    E_id = '<?php echo $eid; ?>'

