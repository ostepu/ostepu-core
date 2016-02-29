<?php
/**
 * @file GetExistsCourseSettings.sql
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2015
 */

/**
 * @file GetExistsCourseSettings.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Setting<?php echo $pre; ?>_<?php echo $courseid; ?>';