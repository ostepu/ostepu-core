<?php
/**
 * @file CleanTransactions.sql
 * removes expired transactions from %Transaction table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @result -
 */
?>

delete from `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>`
where T_durability < UNIX_TIMESTAMP();

