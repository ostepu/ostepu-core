<?php
/**
 * @file GetExistsCourseAttachments.sql
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL version 3
 *
 * @package OSTEPU (https://github.com/ostepu/system)
 * @since 0.1.1
 *
 * @author Till Uhlig <till.uhlig@student.uni-halle.de>
 * @date 2014
 */

/**
 * @file GetExistsCourseAttachments.sql
 * checks whether table exists
 * @author Till Uhlig
 */
?>

show tables like 'Attachment<?php echo $pre; ?>_<?php echo $courseid; ?>';