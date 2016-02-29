<?php
/**
 * @file AddSheetTransaction.sql
 * inserts an transaction into %Transaction table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014-2015
 * @result -
 */
?>

SET @course = (select ES.C_id from `ExerciseSheet` ES where ES.ES_id = <?php echo $esid; ?> limit 1);
SET @statement =
concat("INSERT INTO `Transaction<?php echo $name; ?>_", @course, "` SET <?php echo $object->getInsertData(true); ?>,T_random = '<?php echo $random; ?>';");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';