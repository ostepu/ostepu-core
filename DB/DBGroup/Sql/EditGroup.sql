<?php
/**
 * @file EditGroup.sql
 * updates a specified entry in %Group table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Jörg Baumgarten <kuroi.tatsu@freenet.de>
 * @date 2014
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 *
 * @param int \$esid a %Group identifier
 * @param int \$userid a %Group identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE `Group`
SET <?php echo $values; ?>
WHERE ES_id = '<?php echo $esid; ?>' and U_id_leader = '<?php echo $userid; ?>'