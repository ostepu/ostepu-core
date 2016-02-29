<?php
/**
 * @file GetExistsCourseAttachments.sql
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