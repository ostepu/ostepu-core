<?php
/**
 * @file DeleteMarking.sql
 * deletes a specified marking from %Marking table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @param int \$mid a %Marking identifier
 * @result -
 */
?>

DELETE FROM Marking
WHERE
    M_id = '<?php echo $mid; ?>'