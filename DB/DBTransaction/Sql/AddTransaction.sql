<?php
/**
 * @file AddTransaction.sql
 * inserts an transaction into %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 *
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

INSERT INTO `Transaction<?php echo $profile; ?>_<?php echo $courseid; ?>` SET <?php echo $in->getInsertData(); ?>,T_random = '<?php echo $random; ?>';
select '<?php echo $courseid; ?>' as 'C_id';