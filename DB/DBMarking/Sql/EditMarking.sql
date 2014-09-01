<?php
/**
 * @file EditMarking.sql
 * updates a specified marking from %Marking table
 * @author  Till Uhlig
 * @param int \$mid a %Marking identifier
 * @param string <?php echo $values; ?> the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>
 
UPDATE Marking
SET <?php echo $values; ?>
WHERE M_id = '<?php echo $mid; ?>'