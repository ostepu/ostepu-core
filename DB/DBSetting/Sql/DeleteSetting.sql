<?php
/** 
 * @file DeleteSetting.sql
 * deletes an specified Setting from %Setting table
 * @author  Till Uhlig
 * @param int \$setid a %Setting identifier
 * @result -
 */
?>
 
DELETE FROM `Setting<?php echo $pre; ?>_<?php echo Setting::getCourseFromSettingId($setid); ?>`
WHERE
    SET_id = '<?php echo Setting::getIdFromSettingId($setid); ?>'

