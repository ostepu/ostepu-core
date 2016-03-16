<?php
/**
 * @file GetTransaction.sql
 * gets an specified transaction from %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
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

    