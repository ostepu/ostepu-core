<?php
/**
 * @file AddTransaction.sql
 * inserts an transaction into %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 *
 * @result -
 */
?>

INSERT INTO `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>` SET <?php echo $object->getInsertData(); ?>,T_random = '<?php echo $random; ?>';
select '<?php echo $courseid; ?>' as 'C_id';