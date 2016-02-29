<?php
/**
 * @file GetAmountOfExpiredTransactions.sql
 * counts expired transactions from %Transaction table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015-2016
 * @result
 * - int
 */
?>

SELECT 
  'rows' as 'type',
  count(T.T_id) AS 'amount',
  round((SELECT (data_length+index_length)/table_rows
   FROM information_schema.TABLES
   WHERE table_schema = DATABASE()
     AND TABLE_NAME LIKE 'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>')*count(*),0) AS 'size',
       'Transaction<?php echo $name; ?>_<?php echo $courseid; ?>' AS 'table'
FROM `Transaction<?php echo $name; ?>_<?php echo $courseid; ?>` T
WHERE T.T_durability < UNIX_TIMESTAMP()