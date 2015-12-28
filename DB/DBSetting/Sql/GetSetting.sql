<?php
/**
 * @file GetSetting.sql
 * gets a setting from %Setting table
 * @author Till Uhlig
 * @param int \$setid an %Setting identifier
 * @result
 * - S, the Setting data
 */
?>

select
    S.*,
    concat('<?php echo Setting::getCourseFromSettingId($setid); ?>','_',S.SET_id) as SET_id
from
    `Setting<?php echo $pre; ?>_<?php echo Setting::getCourseFromSettingId($setid); ?>` S
WHERE SET_id = '<?php echo Setting::getIdFromSettingId($setid); ?>'