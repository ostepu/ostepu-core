<?php
/**
 * @file GetSettingByName.sql
 * gets a setting from %Setting table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$setname a %Setting name
 * @param int \$courseid a %Course identifier
 * @result
 * - S, the Setting data
 */
?>

select
    S.*,
    concat('<?php echo $courseid; ?>','_',S.SET_id) as SET_id
from
    `Setting<?php echo $pre; ?>_<?php echo $courseid; ?>` S
WHERE SET_name = '<?php echo $setname; ?>'