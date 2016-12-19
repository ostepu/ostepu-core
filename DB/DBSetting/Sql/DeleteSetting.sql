<?php
/**
 * @file DeleteSetting.sql
 * deletes an specified Setting from %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
 * @param int \$setid a %Setting identifier
 * @result -
 */
?>

<?php $profile = '';
    if (isset($profileName) && trim($profileName) !== ''){
        $profile = '_'.$profileName;
    }?>

DELETE FROM `Setting<?php echo $profile; ?>_<?php echo Setting::getCourseFromSettingId($setid); ?>`
WHERE
    SET_id = '<?php echo Setting::getIdFromSettingId($setid); ?>'

