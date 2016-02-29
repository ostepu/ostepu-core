<?php
/**
 * @file PutComponent.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */
?>

UPDATE Component
SET <?php echo $values; ?>
WHERE CO_id = '<?php echo $componentid; ?>' or CO_name = '<?php echo $componentid; ?>'