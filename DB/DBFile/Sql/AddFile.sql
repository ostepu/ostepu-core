<?php
/**
 * @file AddFile.sql
 * inserts an file into %File table
 * @author  Till Uhlig
 * @param string $values the input data, e.g. 'a=1, b=2'
 * @result -
 */
?>

INSERT INTO File SET <?php echo $values; ?>
ON DUPLICATE KEY UPDATE `F_id`=LAST_INSERT_ID(`F_id`),<?php echo $values; ?>;
SELECT LAST_INSERT_ID() as 'ID';