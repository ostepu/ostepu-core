<?php
/**
 * @file EditAttachment.sql
 * updates an specified attachment from %Attachment table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 *
 * @param int \$aid a %Attachment identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE Attachment
SET <?php echo $values; ?>
WHERE A_id = '<?php echo $aid; ?>'