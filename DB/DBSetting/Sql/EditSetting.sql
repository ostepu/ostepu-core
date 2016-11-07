<?php
/**
 * @file EditSetting.sql
 * updates an specified setting from %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$set a %Setting identifier
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

UPDATE `Setting<?php echo $profile; ?>_<?php echo Setting::getCourseFromSettingId($setid); ?>`
SET <?php echo $in->getInsertData(); ?>
WHERE SET_id = '<?php echo Setting::getIdFromSettingId($setid); ?>'