<?php
/**
 * @file GetCourseSettings.sql
 * gets all course settings from %Setting table
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 * @param int \$courseid an %Course identifier
 * @result
 * - S, the Setting data
 */
?>

select
    S.*,
    concat('<?php echo $courseid; ?>','_',S.SET_id) as SET_id
from
    `Setting<?php echo $pre; ?>_<?php echo $courseid; ?>` S