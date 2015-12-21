<?php
/**
 * @file CleanTransactions.sql
 * removes expired transactions from %Transaction table
 * @author  Till Uhlig
 * @result -
 */
?>

delete from `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>`
where T_durability < UNIX_TIMESTAMP();

