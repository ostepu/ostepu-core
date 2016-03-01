<?php
/**
 * @file AddExerciseTransaction.sql
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

SET @course = (select E.C_id from `Exercise` E where E.E_id = <?php echo $eid; ?> limit 1);
SET @statement =
concat("INSERT INTO `Transaction<?php echo $name; ?>_", @course, "` SET <?php echo $object->getInsertData(true); ?>,T_random = '<?php echo $random; ?>';");
PREPARE stmt1 FROM @statement;
EXECUTE stmt1;
select @course as 'C_id';