<?php
/**
 * @file AddRedirect.sql
 * inserts an Redirect into %Redirect table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 *
 * @result -
 */
?>

INSERT INTO `Redirect<?php echo $pre; ?>_<?php echo $courseid; ?>` SET <?php echo $object->getInsertData(); ?>
ON DUPLICATE KEY UPDATE <?php echo $object->getInsertData(); ?>;
select '<?php echo $courseid; ?>' as 'C_id';