<?php
/**
 * @file GetSettingByName.sql
 * gets a setting from %Setting table
 * @author Till Uhlig
 * @param int \$setname a %Setting name
 * @param int \$courseid a %Course identifier
 * @result 
 * - S, the Setting data
 */
?>
 
select 
    concat('<?php echo $courseid; ?>','_',S.SET_id) as SET_id,
    S.SET_name,
    S.SET_state
from
    `Setting<?php echo $pre; ?>_<?php echo $courseid; ?>` S
WHERE SET_name = '<?php echo $setname; ?>'