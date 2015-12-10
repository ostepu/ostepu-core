<?php
/**
 * @file GetTransaction.sql
 * gets an specified transaction from %Transaction table
 * @author Till Uhlig
 * @param string \$tid a %Transaction identifier
 * @result 
 * - T, the transaction data
 */
?>

select 
    concat('<?php echo Transaction::getCourseFromTransactionId($tid); ?>','_',T.T_id,'_',T.T_random) as T_id,
    T.T_durability,
    T.T_authentication,
    T.T_content
from
    `Transaction<?php echo $name; ?>_<?php echo Transaction::getCourseFromTransactionId($tid); ?>` T
where
    T.T_id = '<?php echo Transaction::getIdFromTransactionId($tid); ?>'
    and ((T.T_authentication is null and '<?php echo $auid; ?>' = '') or T.T_authentication = '<?php echo $auid; ?>')
    and T.T_random = '<?php echo Transaction::getRandomFromTransactionId($tid); ?>'
    and UNIX_TIMESTAMP() <= T.T_durability;

    