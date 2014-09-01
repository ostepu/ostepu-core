<?php
/**
 * @file EditSession.sql
 * updates an specified session from %Session table
 * @author  Till Uhlig
 * @param string $seid a %Session identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
UPDATE `Session`
SET <?php echo $values; ?>
WHERE SE_sessionID = '<?php echo $seid; ?>'