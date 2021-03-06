<?php
/**
 * @file DeleteAttachment.sql
 * deletes an specified attachment from %Attachment table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param int \$aid a %Attachment identifier
 * @result -
 */
?>

DELETE FROM Attachment
WHERE
    A_id = '<?php echo $aid; ?>'

