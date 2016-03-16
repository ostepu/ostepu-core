<?php
/**
 * @file PutComponent.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */
?>

UPDATE Component
SET <?php echo $values; ?>
WHERE CO_id = '<?php echo $componentid; ?>' or CO_name = '<?php echo $componentid; ?>'