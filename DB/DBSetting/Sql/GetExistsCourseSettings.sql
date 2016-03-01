<?php
/**
 * @file GetExistsCourseSettings.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.3.0
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