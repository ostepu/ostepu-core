<?php
/**
 * @file GetExistsCourseRedirects.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.5.0
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2016
 */

/**
 * @file GetExistsCourseRedirects.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Redirect<?php echo $pre; ?>_<?php echo $courseid; ?>';