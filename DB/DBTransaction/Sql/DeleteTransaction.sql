/** 
 * @file DeleteTransaction.sql
 * deletes an specified transaction from %Transaction table
 * @author  Till Uhlig
 * @param string \$tid a %transaction identifier
 * @result -
 */
 
DELETE FROM `Transaction{$name}_".Transaction::getCourseFromTransactionId($tid)."`
WHERE
    T_id = '".Transaction::getIdFromTransactionId($tid)."'
    and ((T.T_authentication is null and '{$auid}' = '') or T.T_authentication = '{$auid}')
    and T_random = '".Transaction::getRandomFromTransactionId($tid)."';

