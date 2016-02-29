<?php
/**
 * @file PutLink.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2013-2014
 */
?>

UPDATE ComponentLinkage
SET <?php echo $values; ?>
WHERE CL_id = '<?php echo $linkid; ?>' or CO_id_owner = (select CO_id from Component where CO_name = '<?php echo $linkid; ?>' limit 1);