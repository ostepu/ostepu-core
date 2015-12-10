<?php
/**
 * @file GetAmountOfExpiredTransactions.sql
 * counts expired transactions from %Transaction table
 * @author Till Uhlig
 * @result 
 * - int
 */
?>

select count(*) as 'amount', 'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>' as 'table' from `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>` 
where T_durability < UNIX_TIMESTAMP();