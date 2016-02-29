<?php
/**
 * @file AddInvitation.sql
 * creates a new entry in %Invitation table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO Invitation SET <?php echo $values; ?>