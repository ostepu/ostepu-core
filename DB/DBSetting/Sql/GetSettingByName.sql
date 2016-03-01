<?php
/**
 * @file GetSettingByName.sql
 * gets a setting from %Setting table
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 *
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