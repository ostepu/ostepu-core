<?php
/**
 * @file DeleteTransaction.sql
 * deletes an specified transaction from %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @param string \$tid a %transaction identifier
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

DELETE FROM `Transaction<?php echo $profile; ?>_<?php echo Transaction::getCourseFromTransactionId($tid); ?>`
WHERE
    T_id = '<?php echo Transaction::getIdFromTransactionId($tid); ?>'
    and T.T_authentication = '<?php echo $auid; ?>'
    and T_random = '<?php echo Transaction::getRandomFromTransactionId($tid); ?>';

