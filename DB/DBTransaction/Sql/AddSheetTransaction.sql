<?php
/**
 * @file AddSheetTransaction.sql
 * inserts an transaction into %Transaction table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.6
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 *
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

SET @course = (select ES.C_id from `ExerciseSheet` ES where ES.ES_id = <?php echo $esid; ?> limit 1);
SET @statement =
concat("INSERT INTO `Transaction<?php echo $profile; ?>_", @course, "` SET <?php echo $in->getInsertData(true); ?>,T_random = '<?php echo $random; ?>';");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';