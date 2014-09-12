<?php
/**
 * @file EditGroup.sql
 * updates a specified entry in %Group table
 * @author  Till Uhlig
 * @param int \$esid a %Group identifier
 * @param int \$userid a %Group identifier
 * @param string \<?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

UPDATE `Group`
SET <?php echo $values; ?>
WHERE ES_id = '<?php echo $esid; ?>' and U_id_leader = '<?php echo $userid; ?>'