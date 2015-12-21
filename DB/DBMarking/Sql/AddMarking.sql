<?php
/**
 * @file AddMarking.sql
 * inserts a marking into %Marking table
 * @author  Till Uhlig
 * @param string \$values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO Marking SET <?php echo $values; ?>