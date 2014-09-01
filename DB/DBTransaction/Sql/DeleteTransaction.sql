<?php
/** 
 * @file DeleteTransaction.sql
 * deletes an specified transaction from %Transaction table
 * @author  Till Uhlig
 * @param string \$tid a %transaction identifier
 * @result -
 */
?>
 
DELETE FROM `Transaction<?php echo $name; ?>_<?php echo Transaction::getCourseFromTransactionId($tid); ?>`
WHERE
    T_id = '<?php echo Transaction::getIdFromTransactionId($tid); ?>'
    and ((T.T_authentication is null and '<?php echo $auid; ?>' = '') or T.T_authentication = '<?php echo $auid; ?>')
    and T_random = '<?php echo Transaction::getRandomFromTransactionId($tid); ?>';

