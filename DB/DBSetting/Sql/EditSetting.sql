<?php
/**
 * @file EditSetting.sql
 * updates an specified setting from %Setting table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$set a %Setting identifier
 * @result -
 */
?>

UPDATE `Setting<?php echo $pre; ?>_<?php echo Setting::getCourseFromSettingId($setid); ?>`
SET <?php echo $object->getInsertData(); ?>
WHERE SET_id = '<?php echo Setting::getIdFromSettingId($setid); ?>'