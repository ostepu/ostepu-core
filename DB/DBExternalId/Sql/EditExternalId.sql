<?php
/**
 * @file EditExternalId.sql
 * updates an specified external id from %ExternalId table
 * @author  Till Uhlig
 * @param string $exid a %ExternalId identifier
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
UPDATE ExternalId
SET <?php echo $values; ?>
WHERE EX_id = '<?php echo $exid; ?>'