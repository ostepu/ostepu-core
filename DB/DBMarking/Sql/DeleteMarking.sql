<?php
/** 
 * @file DeleteMarking.sql
 * deletes a specified marking from %Marking table
 * @author  Till Uhlig
 * @param int \$mid a %Marking identifier
 * @result -
 */
?>
 
DELETE FROM Marking 
WHERE
    M_id = '<?php echo $mid; ?>'