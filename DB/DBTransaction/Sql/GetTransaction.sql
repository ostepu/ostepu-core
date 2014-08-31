/**
 * @file GetTransaction.sql
 * gets an specified transaction from %Transaction table
 * @author Till Uhlig
 * @param string \$tid a %Transaction identifier
 * @result 
 * - T, the transaction data
 */
 
select 
    concat('".Transaction::getCourseFromTransactionId($tid)."','_',T.T_id,'_',T.T_random) as T_id,
    T.T_durability,
    T.T_authentication,
    T.T_content
from
    `Transaction{$name}_".Transaction::getCourseFromTransactionId($tid)."` T
where
    T.T_id = '".Transaction::getIdFromTransactionId($tid)."'
    and (T.T_authentication is null or T.T_authentication = '{$auid}')
    and T.T_random = '".Transaction::getRandomFromTransactionId($tid)."';
    
    