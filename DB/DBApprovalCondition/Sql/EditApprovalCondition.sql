<?php
/**
 * @file EditApprovalCondition.sql
 * updates an specified approval condition from %ApprovalCondition table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 *
 * @param int \$apid a %ApprovalCondition identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE ApprovalCondition
SET <?php echo $values; ?>
WHERE AC_id = '<?php echo $apid; ?>'